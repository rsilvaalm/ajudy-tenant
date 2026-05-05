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

                // ── Customização do tenant ────────────────────────────────
                try {
                    $customization = DB::connection('tenant')->table('customization')->first();
                } catch (\Throwable) {
                    $customization = null;
                }

                View::share('customization', $customization);

                // ── Publicações: lê direto do $currentTenant já compartilhado
                //    pelo InitializeTenancy (que já buscou do landlord com cache)
                try {
                    // $currentTenant já está nas views via view()->share() do middleware
                    $currentTenant = View::shared('currentTenant');

                    $enabled = $currentTenant
                        ? (bool)(int)($currentTenant->publicacoes_enabled ?? 0)
                        : false;

                    $limite = $currentTenant
                        ? (int)($currentTenant->publicacoes_limite_mensal ?? 0)
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
