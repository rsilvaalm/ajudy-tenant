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
            // Apenas executa se o tenant estiver inicializado e a customização ainda não tiver sido composta
            if (tenant('id') && !app()->bound('tenancy_customization_composed')) {
                app()->instance('tenancy_customization_composed', true);

                // ── Customização do tenant ────────────────────────────────
                try {
                    $customization = DB::connection('tenant')->table('customization')->first();
                } catch (\Throwable) {
                    $customization = null;
                }

                View::share('customization', $customization);

                // ── Configurações de publicações (lidas do landlord) ──────
                try {
                    $tenantId = tenant('id');

                    $tenantData = DB::connection('landlord')
                        ->table('tenants')
                        ->where('id', $tenantId)
                        ->select('publicacoes_enabled', 'publicacoes_limite_mensal')
                        ->first();

                    // MySQL retorna TINYINT como string "0"/"1" dependendo do driver.
                    // Cast via (int) garante que "1" vira 1 antes do (bool).
                    $enabled = $tenantData
                        ? (bool) (int) $tenantData->publicacoes_enabled
                        : false;

                    $limite = $tenantData
                        ? (int) $tenantData->publicacoes_limite_mensal
                        : 0;

                    View::share('publicacoesEnabled',      $enabled);
                    View::share('publicacoesLimiteMensal', $limite);

                } catch (\Throwable $e) {
                    Log::debug('ViewServiceProvider publicacoes: ' . $e->getMessage());
                    View::share('publicacoesEnabled',      false);
                    View::share('publicacoesLimiteMensal', 0);
                }
            }
        });
    }
}
