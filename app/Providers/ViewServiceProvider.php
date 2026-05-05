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
            }
        });
    }
}
