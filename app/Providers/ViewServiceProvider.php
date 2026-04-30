<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Injeta $customization em todas as views
        // Usa try/catch pois no login a conexão tenant ainda pode não estar ativa
        View::composer('*', function ($view) {
            if (!app()->bound('tenancy_customization_composed')) {
                app()->instance('tenancy_customization_composed', true);

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
