<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicacoesConfigController extends Controller
{
    public function index(): View
    {
        $currentTenant = view()->shared('currentTenant');

        // Controle do landlord
        $landlordEnabled = (bool)(int)($currentTenant->landlord_publicacoes_enabled ?? 0);
        $landlordLimite  = (int)($currentTenant->landlord_limite_mensal ?? 0);

        // Config local do tenant
        $config = DB::table('publicacoes_config')->first();
        $enabled      = $landlordEnabled && (bool)(int)($config->enabled ?? 0);
        $limiteMensal = (int)($config->limite_mensal ?? 0);

        // Limite efetivo
        $limiteEfetivo = 0;
        if ($landlordLimite > 0 && $limiteMensal > 0) {
            $limiteEfetivo = min($limiteMensal, $landlordLimite);
        } elseif ($landlordLimite > 0) {
            $limiteEfetivo = $landlordLimite;
        } elseif ($limiteMensal > 0) {
            $limiteEfetivo = $limiteMensal;
        }

        $usage   = $this->getMonthlyUsage($limiteEfetivo);
        $history = $this->getHistory();

        return view('pages.publicacoes.config', compact(
            'landlordEnabled', 'landlordLimite',
            'enabled', 'limiteMensal', 'limiteEfetivo',
            'usage', 'history'
        ));
    }

    /**
     * Toggle do tenant (liga/desliga local).
     * Só funciona se landlord habilitou.
     */
    public function toggle(Request $request): RedirectResponse
    {
        $currentTenant   = view()->shared('currentTenant');
        $landlordEnabled = (bool)(int)($currentTenant->landlord_publicacoes_enabled ?? 0);

        if (!$landlordEnabled) {
            return back()->with('error', 'Esta funcionalidade não está disponível no seu plano.');
        }

        $config  = DB::table('publicacoes_config')->first();
        $current = (bool)(int)($config->enabled ?? 0);

        DB::table('publicacoes_config')->updateOrInsert(
            ['id' => $config?->id ?? null],
            ['enabled' => !$current, 'updated_at' => now()]
        );

        app()->forgetInstance('tenancy_customization_composed');

        $msg = !$current ? 'Publicações habilitadas!' : 'Publicações desabilitadas.';
        return back()->with('success', $msg);
    }

    /**
     * Salva o limite mensal definido pelo tenant.
     * Não pode ultrapassar o limite do landlord.
     */
    public function saveLimite(Request $request): RedirectResponse
    {
        $currentTenant  = view()->shared('currentTenant');
        $landlordLimite = (int)($currentTenant->landlord_limite_mensal ?? 0);

        $request->validate([
            'limite_mensal' => 'required|integer|min:0',
        ]);

        $limite = (int) $request->input('limite_mensal');

        // Garante que não ultrapassa o limite do landlord
        if ($landlordLimite > 0 && $limite > $landlordLimite) {
            return back()->with('error', "O limite não pode ultrapassar {$landlordLimite} créditos (definido pelo plano).");
        }

        DB::table('publicacoes_config')->updateOrInsert(
            [],
            ['limite_mensal' => $limite, 'updated_at' => now()]
        );

        app()->forgetInstance('tenancy_customization_composed');

        return back()->with('success', 'Limite atualizado com sucesso!');
    }

    public function usage(): JsonResponse
    {
        $currentTenant  = view()->shared('currentTenant');
        $landlordLimite = (int)($currentTenant->landlord_limite_mensal ?? 0);
        $config         = DB::table('publicacoes_config')->first();
        $tenantLimite   = (int)($config->limite_mensal ?? 0);

        $limiteEfetivo = 0;
        if ($landlordLimite > 0 && $tenantLimite > 0) {
            $limiteEfetivo = min($tenantLimite, $landlordLimite);
        } elseif ($landlordLimite > 0) {
            $limiteEfetivo = $landlordLimite;
        } elseif ($tenantLimite > 0) {
            $limiteEfetivo = $tenantLimite;
        }

        return response()->json($this->getMonthlyUsage($limiteEfetivo));
    }

    private function getMonthlyUsage(int $limite): array
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

        $totalCredits = (int)($queries->total_credits ?? 0);

        return [
            'total_queries'   => (int)($queries->total_queries ?? 0),
            'total_credits'   => $totalCredits,
            'total_results'   => (int)($queries->total_results ?? 0),
            'total_errors'    => (int)($queries->total_errors ?? 0),
            'limit'           => $limite,
            'limit_percent'   => $limite > 0 ? min(round(($totalCredits / $limite) * 100), 100) : 0,
            'limit_remaining' => $limite > 0 ? max($limite - $totalCredits, 0) : null,
            'month'           => now()->format('m/Y'),
            'month_label'     => ucfirst(now()->translatedFormat('F \d\e Y')),
        ];
    }

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
