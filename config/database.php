<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'landlord'),

    'connections' => [

        // ── Conexão central (landlord) ─────────────────────────────────────
        // Usada apenas para resolver o tenant no middleware
        'landlord' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => env('DB_PORT', '3306'),
            'database'  => env('DB_DATABASE', 'ajudy_landlord'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        // ── Conexão dinâmica do tenant ─────────────────────────────────────
        // Preenchida pelo middleware InitializeTenancy em cada request
        'tenant' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'port'      => '3306',
            'database'  => '',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'   => '',
        ],

    ],

    'migrations' => [
        'table'                  => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client'  => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix'  => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],
        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
    ],

];
