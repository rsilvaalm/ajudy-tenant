<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LockScreenController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckModuleAccess;
use App\Http\Middleware\HandleLockScreen;
use App\Http\Middleware\InitializeTenancy;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', InitializeTenancy::class])->group(function () {

    // ── Autenticação ──────────────────────────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

        Route::get('cadastrar-senha',  [SetPasswordController::class, 'show'])->name('set-password.show');
        Route::post('cadastrar-senha', [SetPasswordController::class, 'store'])->name('set-password.store');
    });

    // ── Rotas autenticadas ────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // ── Lock screen ───────────────────────────────────────────────────
        Route::get('lock',           [LockScreenController::class, 'show'])->name('lock');
        Route::post('lock',          [LockScreenController::class, 'unlock'])->name('lock.unlock');
        Route::post('lock/activate', [LockScreenController::class, 'lock'])->name('lock.activate');

        // ── Rotas protegidas ──────────────────────────────────────────────
        Route::middleware(HandleLockScreen::class)->group(function () {

            // Dashboard
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            // ── Perfil do usuário logado ───────────────────────────────────
            Route::get('perfil',           [ProfileUserController::class, 'show'])->name('perfil.show');
            Route::put('perfil',           [ProfileUserController::class, 'update'])->name('perfil.update');
            Route::patch('perfil/senha',   [ProfileUserController::class, 'updatePassword'])->name('perfil.password');

            // ── Gestão de Usuários ─────────────────────────────────────────
            Route::middleware(CheckModuleAccess::class . ':gestao-de-usuarios')
                ->group(function () {

                // Perfis
                Route::get('perfis',             [ProfileController::class, 'index'])->name('perfis.index');
                Route::get('perfis/criar',       [ProfileController::class, 'create'])->name('perfis.create');
                Route::post('perfis',            [ProfileController::class, 'store'])->name('perfis.store');
                Route::get('perfis/{id}/editar', [ProfileController::class, 'edit'])->name('perfis.edit');
                Route::put('perfis/{id}',        [ProfileController::class, 'update'])->name('perfis.update');
                Route::delete('perfis/{id}',     [ProfileController::class, 'destroy'])->name('perfis.destroy');

                // Usuários
                Route::get('usuarios',               [UserController::class, 'index'])->name('usuarios.index');
                Route::get('usuarios/criar',         [UserController::class, 'create'])->name('usuarios.create');
                Route::post('usuarios',              [UserController::class, 'store'])->name('usuarios.store');
                Route::get('usuarios/{id}/editar',   [UserController::class, 'edit'])->name('usuarios.edit');
                Route::put('usuarios/{id}',          [UserController::class, 'update'])->name('usuarios.update');
                Route::delete('usuarios/{id}',       [UserController::class, 'destroy'])->name('usuarios.destroy');
                Route::patch('usuarios/{id}/toggle', [UserController::class, 'toggleActive'])->name('usuarios.toggle');

            });

        });

    });

});
