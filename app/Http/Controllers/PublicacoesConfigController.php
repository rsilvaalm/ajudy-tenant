<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicacoesConfigController extends Controller
{
    /**
     * Página de configurações de publicações do tenant.
     */
    public function index(): View
    {
        // Usa $currentTenant compartilhado pelo InitializeTenancy
        $currentTenant = view()->shared('currentTenant');

        $enabled      = (bool)(int)($currentTenant->publicacoes_enabled      ?? 0);
        $limiteMensal = (int)($currentTenant->publicacoes_limite_mensal ?? 0);

        // Consumo do mês atual (banco do tenant)
        $usage = $this->getMonthlyUsage($limiteMensal);

        // Histórico dos últimos 6 meses
        $history = $this->getHistory();

        return view('pages.publicacoes.config', compact(
            'enabled', 'limiteMensal', 'usage', 'history'
        ));
    }

    /**
     * Liga/desliga publicações pelo tenant.
     */
    public function toggle(Request $request): RedirectResponse
    {
        // Usa $currentTenant compartilhado pelo InitializeTenancy
        $currentTenant = view()->shared('currentTenant');

        if (!$currentTenant) {
            return back()->with('error', 'Tenant não identificado.');
        }

        $tenantId = $currentTenant->id;

        // Verifica se tem token configurado
        $tenantData = DB::connection('landlord')
            ->table('tenants')
            ->where('id', $tenantId)
            ->first(['publicacoes_enabled', 'escavador_api_key']);

        if (empty($tenantData->escavador_api_key)) {
            return back()->with('error', 'Este recurso não está disponível. Contate o suporte para configurar o acesso.');
        }

        $current = (bool)(int)($tenantData->publicacoes_enabled ?? 0);

        DB::connection('landlord')
            ->table('tenants')
            ->where('id', $tenantId)
            ->update(['publicacoes_enabled' => !$current]);

        // Invalida o cache do InitializeTenancy para este host
        $host = $request->getHost();
        \Illuminate\Support\Facades\Cache::forget("tenant_connection:{$host}");

        // Limpa o bind do ViewServiceProvider para reexecutar no próximo request
        app()->forgetInstance('tenancy_customization_composed');

        $msg = !$current ? 'Publicações habilitadas!' : 'Publicações desabilitadas.';
        return back()->with('success', $msg);
    }

    /**
     * Retorna dados de consumo via JSON (para atualização sem reload).
     */
    public function usage(): JsonResponse
    {
        $currentTenant = view()->shared('currentTenant');
        $limiteMensal  = (int)($currentTenant->publicacoes_limite_mensal ?? 0);

        return response()->json($this->getMonthlyUsage($limiteMensal));
    }

    /**
     * Consumo do mês atual.
     */
    private function getMonthlyUsage(int $limit): array
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        try {
            $queries = DB::table('publication_queries')
                ->whereBetween('queried_at', [$start, $end])
                ->selectRaw('
                    COUNT(*) as total_queries,
                    COALESCE(SUM(credits_used), 0) as total_credits,
                    COALESCE(SUM(results_count), 0) as total_results,
                    SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as total_errors
                ')
                ->first();
        } catch (\Throwable) {
            $queries = null;
        }

        $totalCredits = (int) ($queries->total_credits ?? 0);

        return [
            'total_queries'  => (int) ($queries->total_queries  ?? 0),
            'total_credits'  => $totalCredits,
            'total_results'  => (int) ($queries->total_results  ?? 0),
            'total_errors'   => (int) ($queries->total_errors   ?? 0),
            'limit'          => $limit,
            'limit_percent'  => $limit > 0 ? min(round(($totalCredits / $limit) * 100), 100) : 0,
            'limit_remaining'=> $limit > 0 ? max($limit - $totalCredits, 0) : null,
            'month'          => now()->format('m/Y'),
            'month_label'    => ucfirst(now()->translatedFormat('F \d\e Y')),
        ];
    }

    /**
     * Histórico dos últimos 6 meses agrupado por mês.
     */
    private function getHistory(): array
    {
        try {
            return DB::table('publication_queries')
                ->where('queried_at', '>=', now()->subMonths(6)->startOfMonth())
                ->selectRaw('
                    DATE_FORMAT(queried_at, "%Y-%m") as month_key,
                    DATE_FORMAT(queried_at, "%m/%Y") as month_label,
                    COUNT(*) as total_queries,
                    COALESCE(SUM(credits_used), 0) as total_credits,
                    COALESCE(SUM(results_count), 0) as total_results
                ')
                ->groupBy('month_key', 'month_label')
                ->orderByDesc('month_key')
                ->get()
                ->toArray();
        } catch (\Throwable) {
            return [];
        }
    }
}
