@extends('layouts.app')
@section('title', 'Processo ' . $process->number)
@section('page-title', 'Processo')

@section('page-actions')
<a href="{{ route('processos.edit', $process->id) }}" class="btn btn-sm btn-light">
    <i class="ri-pencil-line me-1"></i> Editar
</a>
@endsection

@section('content')

{{-- Cabeçalho compacto --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <p class="text-muted fs-11 mb-0 text-uppercase letter-spacing-1">Número do processo</p>
                <h5 class="fw-bold mb-0">{{ $process->number }}</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if($process->folder_name)
                <span class="badge fs-12"
                      style="background:{{ $process->folder_color ?? '#1a3c5e' }};color:#fff;padding:6px 12px;">
                    <i class="ri-folder-line me-1"></i>{{ $process->folder_name }}
                </span>
                @endif
                <span class="text-muted fs-13">{{ \Carbon\Carbon::parse($process->date)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Abas --}}
<div class="card">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active py-3" data-bs-toggle="tab" href="#tab-info" role="tab">
                    <i class="ri-information-line me-1"></i>
                    <span class="d-none d-sm-inline">Informações</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-agenda" role="tab">
                    <i class="ri-calendar-line me-1"></i>
                    <span class="d-none d-sm-inline">Agendamentos</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-andamento" role="tab">
                    <i class="ri-git-commit-line me-1"></i>
                    <span class="d-none d-sm-inline">Andamento</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-despesas" role="tab">
                    <i class="ri-money-dollar-circle-line me-1"></i>
                    <span class="d-none d-sm-inline">Despesas</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-honorarios" role="tab">
                    <i class="ri-hand-coin-line me-1"></i>
                    <span class="d-none d-sm-inline">Honorários</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body tab-content p-4">

        {{-- Aba: Informações --}}
        <div class="tab-pane active" id="tab-info" role="tabpanel">
            <div class="row g-4">

                {{-- 70% — Dados do processo --}}
                <div class="col-lg-8">
                    <h6 class="fw-semibold text-muted text-uppercase fs-11 mb-3">Dados do processo</h6>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Polo ativo</label>
                            <p class="fw-medium mb-0">{{ $process->active_pole }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Polo passivo</label>
                            <p class="fw-medium mb-0">{{ $process->passive_pole }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Local</label>
                            <p class="fw-medium mb-0 text-muted">{{ $process->location ?: '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Data</label>
                            <p class="fw-medium mb-0">{{ \Carbon\Carbon::parse($process->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- 30% — Cliente --}}
                <div class="col-lg-4">
                    <h6 class="fw-semibold text-muted text-uppercase fs-11 mb-3">Cliente</h6>
                    <div class="p-3 rounded border d-flex align-items-center gap-3">
                        <div class="avatar-sm flex-shrink-0">
                            <div class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold fs-16">
                                {{ strtoupper(substr($process->client_name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <p class="fw-semibold mb-0 text-truncate">{{ $process->client_name }}</p>
                        </div>
                        <a href="{{ route('clientes.show', $process->client_id) }}"
                           class="btn btn-sm btn-soft-primary flex-shrink-0" title="Ver ficha do cliente">
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Aba: Agendamentos --}}
        <div class="tab-pane" id="tab-agenda" role="tabpanel">
            <div class="text-center text-muted py-5">
                <i class="ri-calendar-line fs-48 d-block mb-2 opacity-25"></i>
                <p class="mb-0">Módulo de agendamentos em desenvolvimento.</p>
            </div>
        </div>

        {{-- Aba: Andamento --}}
        <div class="tab-pane" id="tab-andamento" role="tabpanel">
            <div class="text-center text-muted py-5">
                <i class="ri-git-commit-line fs-48 d-block mb-2 opacity-25"></i>
                <p class="mb-0">Módulo de andamento em desenvolvimento.</p>
            </div>
        </div>

        {{-- Aba: Despesas --}}
        <div class="tab-pane" id="tab-despesas" role="tabpanel">
            <div class="text-center text-muted py-5">
                <i class="ri-money-dollar-circle-line fs-48 d-block mb-2 opacity-25"></i>
                <p class="mb-0">Módulo de despesas em desenvolvimento.</p>
            </div>
        </div>

        {{-- Aba: Honorários --}}
        <div class="tab-pane" id="tab-honorarios" role="tabpanel">
            <div class="text-center text-muted py-5">
                <i class="ri-hand-coin-line fs-48 d-block mb-2 opacity-25"></i>
                <p class="mb-0">Módulo de honorários em desenvolvimento.</p>
            </div>
        </div>

    </div>
</div>

@endsection
