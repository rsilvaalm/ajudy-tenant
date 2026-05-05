<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LockScreenController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ProcessMovementController;
use App\Http\Controllers\ProcessPublicationController;
use App\Http\Controllers\PublicacoesConfigController;
use App\Http\Controllers\ProcessNoteController;
use App\Http\Controllers\ScheduleTypeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckModuleAccess;
use App\Http\Middleware\HandleLockScreen;
use App\Http\Middleware\InitializeTenancy;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', InitializeTenancy::class])->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
        Route::get('cadastrar-senha',  [SetPasswordController::class, 'show'])->name('set-password.show');
        Route::post('cadastrar-senha', [SetPasswordController::class, 'store'])->name('set-password.store');
    });

    Route::middleware('auth')->group(function () {

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('lock',           [LockScreenController::class, 'show'])->name('lock');
        Route::post('lock',          [LockScreenController::class, 'unlock'])->name('lock.unlock');
        Route::post('lock/activate', [LockScreenController::class, 'lock'])->name('lock.activate');

        Route::middleware(HandleLockScreen::class)->group(function () {

            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('perfil',         [ProfileUserController::class, 'show'])->name('perfil.show');
            Route::put('perfil',         [ProfileUserController::class, 'update'])->name('perfil.update');
            Route::patch('perfil/senha', [ProfileUserController::class, 'updatePassword'])->name('perfil.password');

            // Atendimento rápido
            Route::get('atendimento/atual',           [AttendanceController::class, 'current'])->name('atendimento.current');
            Route::get('atendimento/buscar-clientes', [AttendanceController::class, 'searchClients'])->name('atendimento.search');
            Route::post('atendimento',                [AttendanceController::class, 'store'])->name('atendimento.store');
            Route::patch('atendimento/{id}/encerrar', [AttendanceController::class, 'close'])->name('atendimento.close');

            // Listagem de atendimentos
            Route::get('atendimentos',                    [AttendanceListController::class, 'index'])->name('atendimentos.index');
            Route::get('atendimentos/cliente/{clientId}', [AttendanceListController::class, 'byClient'])->name('atendimentos.byClient');
            Route::get('atendimentos/{id}',               [AttendanceListController::class, 'show'])->name('atendimentos.show');
            Route::delete('atendimentos/{id}',            [AttendanceListController::class, 'destroy'])->name('atendimentos.destroy');

            // Gestão de Usuários
            // Configurações de publicações (acesso para qualquer usuário autenticado)
            Route::get('configuracoes/publicacoes',         [PublicacoesConfigController::class, 'index'])->name('publicacoes.config.index');
            Route::post('configuracoes/publicacoes/toggle', [PublicacoesConfigController::class, 'toggle'])->name('publicacoes.config.toggle');
            Route::get('configuracoes/publicacoes/usage',   [PublicacoesConfigController::class, 'usage'])->name('publicacoes.config.usage');

            Route::middleware(CheckModuleAccess::class . ':gestao-de-usuarios')->group(function () {
                Route::get('perfis',             [ProfileController::class, 'index'])->name('perfis.index');
                Route::get('perfis/criar',       [ProfileController::class, 'create'])->name('perfis.create');
                Route::post('perfis',            [ProfileController::class, 'store'])->name('perfis.store');
                Route::get('perfis/{id}/editar', [ProfileController::class, 'edit'])->name('perfis.edit');
                Route::put('perfis/{id}',        [ProfileController::class, 'update'])->name('perfis.update');
                Route::delete('perfis/{id}',     [ProfileController::class, 'destroy'])->name('perfis.destroy');

                Route::get('usuarios',               [UserController::class, 'index'])->name('usuarios.index');
                Route::get('usuarios/criar',         [UserController::class, 'create'])->name('usuarios.create');
                Route::post('usuarios',              [UserController::class, 'store'])->name('usuarios.store');
                Route::get('usuarios/{id}/editar',   [UserController::class, 'edit'])->name('usuarios.edit');
                Route::put('usuarios/{id}',          [UserController::class, 'update'])->name('usuarios.update');
                Route::delete('usuarios/{id}',       [UserController::class, 'destroy'])->name('usuarios.destroy');
                Route::patch('usuarios/{id}/toggle', [UserController::class, 'toggleActive'])->name('usuarios.toggle');
            });

            // Clientes + Processos + Agendamentos
            Route::middleware(CheckModuleAccess::class . ':clientes')->group(function () {

                // Campos personalizados
                Route::get('clientes/campos',               [CustomFieldController::class, 'index'])->name('clientes.campos.index');
                Route::post('clientes/campos',              [CustomFieldController::class, 'store'])->name('clientes.campos.store');
                Route::put('clientes/campos/{id}',          [CustomFieldController::class, 'update'])->name('clientes.campos.update');
                Route::patch('clientes/campos/{id}/toggle', [CustomFieldController::class, 'toggleActive'])->name('clientes.campos.toggle');
                Route::delete('clientes/campos/{id}',       [CustomFieldController::class, 'destroy'])->name('clientes.campos.destroy');

                // CRUD Clientes
                Route::get('clientes',                 [ClientController::class, 'index'])->name('clientes.index');
                Route::get('clientes/novo',            [ClientController::class, 'create'])->name('clientes.create');
                Route::post('clientes',                [ClientController::class, 'store'])->name('clientes.store');
                Route::get('clientes/{client}',        [ClientController::class, 'show'])->name('clientes.show');
                Route::get('clientes/{client}/editar', [ClientController::class, 'edit'])->name('clientes.edit');
                Route::put('clientes/{client}',        [ClientController::class, 'update'])->name('clientes.update');
                Route::delete('clientes/{client}',     [ClientController::class, 'destroy'])->name('clientes.destroy');

                // Pastas
                Route::get('processos/pastas-json',    [FolderController::class, 'json'])->name('processos.pastas.json');
                Route::get('processos/pastas',         [FolderController::class, 'index'])->name('processos.pastas.index');
                Route::post('processos/pastas',        [FolderController::class, 'store'])->name('processos.pastas.store');
                Route::put('processos/pastas/{id}',    [FolderController::class, 'update'])->name('processos.pastas.update');
                Route::delete('processos/pastas/{id}', [FolderController::class, 'destroy'])->name('processos.pastas.destroy');

                // Processos — estáticas antes das dinâmicas
                Route::get('processos/novo',         [ProcessController::class, 'create'])->name('processos.create');
                Route::get('processos/buscar',       [ProcessController::class, 'search'])->name('processos.buscar');
                Route::get('processos/cliente/{id}', [ProcessController::class, 'byClient'])->name('processos.byClient');
                // Anotações do processo — estáticas ANTES de processos/{id}
                Route::get('processos/{processId}/anotacoes',    [ProcessNoteController::class, 'byProcess'])->name('processos.notas.list');
                Route::post('processos/anotacoes',               [ProcessNoteController::class, 'store'])->name('processos.notas.store');
                // Publicações (Escavador)
                Route::get('processos/{processId}/publicacoes',       [ProcessPublicationController::class, 'index'])->name('processos.publicacoes.index');
                Route::post('processos/{processId}/publicacoes/sync',  [ProcessPublicationController::class, 'sync'])->name('processos.publicacoes.sync');

                // Movimentações DataJud
                Route::get('processos/{processId}/movimentacoes',  [ProcessMovementController::class, 'index'])->name('processos.movimentacoes.index');
                Route::post('processos/{processId}/movimentacoes/sync', [ProcessMovementController::class, 'sync'])->name('processos.movimentacoes.sync');

                Route::put('processos/anotacoes/{id}',           [ProcessNoteController::class, 'update'])->name('processos.notas.update');
                Route::delete('processos/anotacoes/{id}',        [ProcessNoteController::class, 'destroy'])->name('processos.notas.destroy');

                Route::post('processos',             [ProcessController::class, 'store'])->name('processos.store');
                Route::get('processos/{id}',         [ProcessController::class, 'show'])->name('processos.show');
                Route::get('processos/{id}/editar',  [ProcessController::class, 'edit'])->name('processos.edit');
                Route::put('processos/{id}',         [ProcessController::class, 'update'])->name('processos.update');
                Route::delete('processos/{id}',      [ProcessController::class, 'destroy'])->name('processos.destroy');

                // Tipos de agendamento
                Route::get('agendamentos/tipos-json',    [ScheduleTypeController::class, 'json'])->name('agendamentos.tipos.json');
                Route::get('agendamentos/tipos',         [ScheduleTypeController::class, 'index'])->name('agendamentos.tipos.index');
                Route::post('agendamentos/tipos',        [ScheduleTypeController::class, 'store'])->name('agendamentos.tipos.store');
                Route::put('agendamentos/tipos/{id}',    [ScheduleTypeController::class, 'update'])->name('agendamentos.tipos.update');
                Route::delete('agendamentos/tipos/{id}', [ScheduleTypeController::class, 'destroy'])->name('agendamentos.tipos.destroy');

                // Agendamentos
                Route::get('agendamentos/processo/{processId}', [ScheduleController::class, 'byProcess'])->name('agendamentos.byProcess');
                Route::get('agendamentos/buscar-usuarios',      [ScheduleController::class, 'searchUsers'])->name('agendamentos.searchUsers');
                Route::post('agendamentos',                     [ScheduleController::class, 'store'])->name('agendamentos.store');
                Route::patch('agendamentos/{id}/concluir',      [ScheduleController::class, 'complete'])->name('agendamentos.complete');
                Route::patch('agendamentos/{id}/reabrir',       [ScheduleController::class, 'reopen'])->name('agendamentos.reopen');
                Route::delete('agendamentos/{id}',              [ScheduleController::class, 'destroy'])->name('agendamentos.destroy');


            });

        });
    });
});
