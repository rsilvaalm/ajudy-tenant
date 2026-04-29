<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LockScreenController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\HandleLockScreen;
use App\Http\Middleware\InitializeTenancy;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', InitializeTenancy::class])->group(function () {

    // ── Autenticação (apenas para não autenticados) ────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    // ── Rotas autenticadas ────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // ── Lock screen (sem HandleLockScreen para evitar loop) ───────────
        Route::get('lock',         [LockScreenController::class, 'show'])->name('lock');
        Route::post('lock',        [LockScreenController::class, 'unlock'])->name('lock.unlock');
        Route::post('lock/activate', [LockScreenController::class, 'lock'])->name('lock.activate');

        // ── Rotas protegidas — bloqueadas se lock ativo ───────────────────
        Route::middleware(HandleLockScreen::class)->group(function () {

            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            // Novos módulos virão aqui

        });

    });

});
