<?php

namespace App\Http\Controllers;

use App\Services\EscavadorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProcessPublicationController extends Controller
{
    public function __construct(private EscavadorService $escavador) {}

    /**
     * Retorna publicações já salvas + consumo do mês.
     */
    public function index(int $processId): JsonResponse
    {
        $publications = DB::table('process_publications')
            ->where('process_id', $processId)
            ->orderByDesc('publication_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'publications' => $publications,
            'usage'        => $this->getMonthlyUsage(),
        ]);
    }

    /**
     * Consulta o Escavador, verifica limites, persiste e retorna publicações.
     */
    public function sync(int $processId): JsonResponse
    {
        // Usa currentTenant do InitializeTenancy
        $tenant = view()->shared('currentTenant');

        // 1. Verifica se publicações estão habilitadas pelo landlord
        if (!$tenant || !(bool)(int)($tenant->publicacoes_enabled ?? 0)) {
            return response()->json([
                'error'   => 'disabled',
                'message' => 'O módulo de publicações não está habilitado para este tenant.',
            ], 403);
        }

        // 2. Verifica se tem token configurado (busca direto pois cache não inclui api_key por segurança)
        $tenantWithKey = DB::connection('landlord')
            ->table('tenants')
            ->where('id', $tenant->id)
            ->value('escavador_api_key');

        if (empty($tenantWithKey)) {
            return response()->json([
                'error'   => 'no_token',
                'message' => 'Token do Escavador não configurado. Contate o suporte.',
            ], 422);
        }

        // 3. Verifica limite mensal de créditos (controle interno do Ajudy)
        $usage = $this->getMonthlyUsage();
        $limit = (int) ($tenant->publicacoes_limite_mensal ?? 0);

        if ($limit > 0 && $usage['total_credits'] >= $limit) {
            return response()->json([
                'error'   => 'limit_reached',
                'message' => "Limite mensal de {$limit} crédito(s) atingido. Aguarde o próximo ciclo ou contate o suporte.",
                'usage'   => $usage,
            ], 429);
        }

        // 4. Busca o processo
        $process = DB::table('processes')
            ->where('id', $processId)->whereNull('deleted_at')->first();

        if (!$process) {
            return response()->json(['error' => 'not_found'], 404);
        }

        // 5. Consulta Escavador com o token do tenant
        try {
            $result = $this->escavador->getPublications($process->number);
        } catch (\RuntimeException $e) {
            // Loga a tentativa mesmo com erro
            $this->logQuery($processId, $process->number, 1, 'error', $e->getMessage(), 0);

            return response()->json([
                'error'   => 'api_error',
                'message' => $e->getMessage(),
            ], 422);
        }

        $publications = $result['publications'];
        $creditsUsed  = $result['credits_used'];

        // 6. Loga a consulta para controle de custos interno
        $this->logQuery($processId, $process->number, $creditsUsed, empty($publications) ? 'empty' : 'success', null, count($publications));

        // 7. Persiste novas publicações (sem duplicar pelo external_id)
        $saved = 0;
        foreach ($publications as $pub) {
            if (empty($pub['external_id'])) continue;

            $exists = DB::table('process_publications')
                ->where('process_id', $processId)
                ->where('external_id', $pub['external_id'])
                ->exists();

            if (!$exists) {
                DB::table('process_publications')->insert([
                    'process_id'       => $processId,
                    'publication_date' => $pub['publication_date'],
                    'source'           => $pub['source'],
                    'diario'           => $pub['diario'],
                    'caderno'          => $pub['caderno'],
                    'content'          => $pub['content'],
                    'external_id'      => $pub['external_id'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
                $saved++;
            }
        }

        // 8. Retorna tudo atualizado
        $stored = DB::table('process_publications')
            ->where('process_id', $processId)
            ->orderByDesc('publication_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success'      => true,
            'synced'       => $saved,
            'total'        => $stored->count(),
            'publications' => $stored,
            'credits_used' => $creditsUsed,
            'usage'        => $this->getMonthlyUsage(),
        ]);
    }

    /**
     * Consumo do tenant no mês corrente — controle interno do Ajudy.
     * Independe do que o Escavador mostra no painel dele.
     */
    private function getMonthlyUsage(): array
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        $queries = DB::table('publication_queries')
            ->whereBetween('queried_at', [$start, $end])
            ->selectRaw('COUNT(*) as total_queries, COALESCE(SUM(credits_used),0) as total_credits, COALESCE(SUM(results_count),0) as total_results')
            ->first();

        $currentTenant = view()->shared('currentTenant');
        $limit = (int)($currentTenant->publicacoes_limite_mensal ?? 0);

        $totalCredits = (int) ($queries->total_credits ?? 0);

        return [
            'total_queries'  => (int) ($queries->total_queries ?? 0),
            'total_credits'  => $totalCredits,
            'total_results'  => (int) ($queries->total_results ?? 0),
            'limit'          => $limit,
            'limit_percent'  => $limit > 0 ? min(round(($totalCredits / $limit) * 100), 100) : 0,
            'month'          => now()->format('m/Y'),
        ];
    }

    private function logQuery(int $processId, string $number, int $credits, string $status, ?string $error, int $results): void
    {
        DB::table('publication_queries')->insert([
            'process_id'     => $processId,
            'user_id'        => auth()->id(),
            'process_number' => $number,
            'credits_used'   => $credits,
            'status'         => $status,
            'error_message'  => $error,
            'results_count'  => $results,
            'queried_at'     => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
