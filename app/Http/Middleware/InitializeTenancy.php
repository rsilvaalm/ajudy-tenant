<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancy
{
    public function handle(Request $request, Closure $next): Response
    {
        $host     = $request->getHost();
        $cacheKey = "tenant_connection:{$host}";

        // ── Busca o updated_at do tenant (query leve, sem cache)
        // Serve como "versão" — quando o landlord salva qualquer coisa no tenant,
        // o updated_at muda, o versionedKey muda e o cache antigo é ignorado.
        $updatedAt = DB::connection('landlord')
            ->table('domains')
            ->join('tenants', 'tenants.id', '=', 'domains.tenant_id')
            ->where('domains.domain', $host)
            ->value('tenants.updated_at');

        $versionedKey = $cacheKey . ':' . ($updatedAt ?? 'none');

        // ── Busca dados completos com cache versionado (TTL 1 hora)
        $tenantData = Cache::remember($versionedKey, now()->addHour(), function () use ($host) {
            return DB::connection('landlord')
                ->table('domains')
                ->join('tenants', 'tenants.id', '=', 'domains.tenant_id')
                ->where('domains.domain', $host)
                ->where('tenants.is_active', true)
                ->select(
                    'tenants.id',
                    'tenants.name',
                    'tenants.data',
                    'tenants.color_primary',
                    'tenants.color_secondary',
                    'tenants.color_tertiary',
                    'tenants.logo_primary',
                    'tenants.logo_vertical',
                    'tenants.logo_negative',
                    'tenants.db_processed_at',
                    'tenants.publicacoes_enabled',
                    'tenants.publicacoes_limite_mensal'
                )
                ->first();
        });

        if (!$tenantData) {
            abort(404, 'Tenant não encontrado ou inativo.');
        }

        // ── APP_URL dinâmico baseado no subdomínio atual ──────────────────
        Config::set('app.url', $request->getScheme() . '://' . $host);

        // Extrai dados de conexão do campo data (JSON)
        $data   = json_decode($tenantData->data ?? '{}', true) ?? [];
        $dbHost = $data['db_host']     ?? $data['tenancy_db_host']     ?? config('database.connections.landlord.host');
        $dbPort = $data['db_port']     ?? $data['tenancy_db_port']     ?? config('database.connections.landlord.port');
        $dbUser = $data['db_username'] ?? $data['tenancy_db_username'] ?? config('database.connections.landlord.username');
        $dbPass = $data['db_password'] ?? $data['tenancy_db_password'] ?? config('database.connections.landlord.password');
        $dbName = $data['db_name']     ?? $data['tenancy_db_name']     ?? null;

        if (!$dbName) {
            abort(503, 'Banco de dados do tenant não configurado.');
        }

        // Registra conexão dinâmica do tenant
        Config::set('database.connections.tenant', [
            'driver'    => 'mysql',
            'host'      => $dbHost,
            'port'      => $dbPort,
            'database'  => $dbName,
            'username'  => $dbUser,
            'password'  => $dbPass,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);

        // Troca a conexão default para o banco do tenant
        Config::set('database.default', 'tenant');
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Compartilha dados do tenant com todas as views
        view()->share('currentTenant', $tenantData);

        return $next($request);
    }
}
