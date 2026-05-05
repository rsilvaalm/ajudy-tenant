<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (!app()->bound('tenancy_customization_composed')) {
                app()->instance('tenancy_customization_composed', true);

                // ── Customização visual do tenant ─────────────────────────
                try {
                    $customization = DB::connection('tenant')->table('customization')->first();
                } catch (\Throwable) {
                    $customization = null;
                }
                View::share('customization', $customization);

                // ── Publicações ───────────────────────────────────────────
                // Regras:
                // 1. landlord_publicacoes_enabled = false → bloqueia tudo (upsell)
                // 2. landlord_publicacoes_enabled = true  → respeita config do tenant
                // 3. tenant.enabled = true                → publicações ativas
                try {
                    $currentTenant = View::shared('currentTenant');

                    // Controle global do landlord
                    $landlordEnabled = (bool)(int)($currentTenant->landlord_publicacoes_enabled ?? 0);
                    $landlordLimite  = (int)($currentTenant->landlord_limite_mensal ?? 0);

                    if (!$landlordEnabled) {
                        // Landlord desabilitou — mostra upsell, não mostra dados
                        View::share('publicacoesEnabled',      false);
                        View::share('publicacoesBloqueadas',   true);  // flag para exibir upsell
                        View::share('publicacoesLimiteMensal', 0);
                        return;
                    }

                    // Landlord habilitou — lê config do banco do tenant
                    $config = DB::connection('tenant')
                        ->table('publicacoes_config')
                        ->first();

                    $tenantEnabled = (bool)(int)($config->enabled ?? 0);
                    $tenantLimite  = (int)($config->limite_mensal ?? 0);

                    // Limite efetivo: usa o do tenant se menor que o do landlord,
                    // caso contrário usa o do landlord. 0 = sem limite.
                    $limiteEfetivo = 0;
                    if ($landlordLimite > 0 && $tenantLimite > 0) {
                        $limiteEfetivo = min($tenantLimite, $landlordLimite);
                    } elseif ($landlordLimite > 0) {
                        $limiteEfetivo = $landlordLimite;
                    } elseif ($tenantLimite > 0) {
                        $limiteEfetivo = $tenantLimite;
                    }

                    View::share('publicacoesEnabled',      $tenantEnabled);
                    View::share('publicacoesBloqueadas',   false);
                    View::share('publicacoesLimiteMensal', $limiteEfetivo);
                    View::share('landlordLimiteMensal',    $landlordLimite);

                } catch (\Throwable $e) {
                    Log::debug('ViewServiceProvider publicacoes: ' . $e->getMessage());
                    View::share('publicacoesEnabled',      false);
                    View::share('publicacoesBloqueadas',   false);
                    View::share('publicacoesLimiteMensal', 0);
                    View::share('landlordLimiteMensal',    0);
                }
            }
        });
    }
}
