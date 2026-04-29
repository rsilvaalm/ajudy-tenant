<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\InitializeTenancy;
use Illuminate\Support\Facades\Route;

// Todas as rotas passam pelo middleware de tenancy
Route::middleware(InitializeTenancy::class)->group(function () {

    // ── Autenticação ──────────────────────────────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // ── Dashboard ─────────────────────────────────────────────────────────
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

});
