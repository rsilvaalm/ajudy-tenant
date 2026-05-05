@extends('layouts.app')
@section('title', 'Publicações')
@section('page-title', 'Publicações')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible d-flex gap-2 align-items-center mb-4" role="alert">
    <i class="ri-checkbox-circle-line fs-16 flex-shrink-0"></i>
    {{ session('success') }}
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible d-flex gap-2 align-items-center mb-4" role="alert">
    <i class="ri-error-warning-line fs-16 flex-shrink-0"></i>
    {{ session('error') }}
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if(!$landlordEnabled)

    {{-- ══════════════════════════ UPSELL ══════════════════════════ --}}
    <div class="card">
        <div class="card-body py-5 text-center">
            <div class="avatar-xl mx-auto mb-4">
                <div class="avatar-title rounded-circle fs-36"
                     style="background:rgba(var(--brand-primary-rgb, 26,60,94),.08);color:var(--brand-primary,#1a3c5e);">
                    <i class="ri-newspaper-line"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-2">Monitoramento de Publicações</h4>
            <p class="text-muted mb-1 mx-auto" style="max-width:480px;">
                Acompanhe automaticamente as publicações do Diário da Justiça Eletrônico (DJe)
                diretamente na ficha de cada processo — sem precisar acessar os portais dos tribunais.
            </p>
            <div class="row g-3 justify-content-center mt-3 mb-4">
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2 text-muted fs-13">
                        <i class="ri-check-line text-success fs-16"></i> Cobertura nacional
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2 text-muted fs-13">
                        <i class="ri-check-line text-success fs-16"></i> Atualização diária
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2 text-muted fs-13">
                        <i class="ri-check-line text-success fs-16"></i> Integrado ao processo
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2 text-muted fs-13">
                        <i class="ri-check-line text-success fs-16"></i> Controle de consumo
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 justify-content-center">
                <a href="mailto:contato@ajudy.com.br?subject=Quero contratar Publicações"
                   class="btn btn-primary px-4">
                    <i class="ri-mail-send-line me-1"></i> Solicitar ativação
                </a>
                <a href="https://ajudy.com.br" target="_blank" class="btn btn-light px-4">
                    Saiba mais
                </a>
            </div>
            <p class="text-muted fs-12 mt-4 mb-0">
                Entre em contato com o suporte para ativar esta funcionalidade no seu plano.
            </p>
        </div>
    </div>

@else

    {{-- ══════════════════════════ CONFIGURAÇÕES ══════════════════════════ --}}
    <div class="row g-4">

        {{-- Status e toggle --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Status</h5>
                    @if($enabled)
                        <span class="badge bg-success-subtle text-success fs-12">
                            <i class="ri-checkbox-circle-line me-1"></i>Ativo
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary fs-12">
                            <i class="ri-close-circle-line me-1"></i>Inativo
                        </span>
                    @endif
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="text-center py-3">
                        @if($enabled)
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title rounded-circle bg-success-subtle text-success fs-36">
                                    <i class="ri-newspaper-line"></i>
                                </div>
                            </div>
                            <p class="fw-semibold mb-1">Publicações ativas</p>
                            <p class="text-muted fs-13 mb-0">
                                A aba <strong>Publicações</strong> está disponível na ficha de cada processo.
                            </p>
                        @else
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title rounded-circle bg-light text-muted fs-36">
                                    <i class="ri-newspaper-line"></i>
                                </div>
                            </div>
                            <p class="fw-semibold mb-1 text-muted">Publicações inativas</p>
                            <p class="text-muted fs-13 mb-0">
                                Habilite para consultar publicações do DJe diretamente na ficha do processo.
                            </p>
                        @endif
                    </div>
                    <div class="mt-auto pt-3">
                        <form method="POST" action="{{ route('publicacoes.config.toggle') }}">
                            @csrf
                            <button type="submit"
                                    class="btn w-100 {{ $enabled ? 'btn-soft-danger' : 'btn-primary' }}">
                                @if($enabled)
                                    <i class="ri-pause-circle-line me-1"></i> Desabilitar
                                @else
                                    <i class="ri-play-circle-line me-1"></i> Habilitar publicações
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Limite mensal --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Limite de créditos</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted fs-13 mb-3">
                        Defina um limite mensal de créditos para controlar o consumo.
                        @if($landlordLimite > 0)
                            O limite máximo do seu plano é <strong>{{ $landlordLimite }}</strong> créditos.
                        @else
                            Seu plano não possui limite máximo definido.
                        @endif
                    </p>
                    <form method="POST" action="{{ route('publicacoes.config.limite') }}" class="d-flex flex-column flex-grow-1">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Limite mensal</label>
                            <div class="input-group">
                                <input type="number" name="limite_mensal"
                                       class="form-control @error('limite_mensal') is-invalid @enderror"
                                       value="{{ $limiteMensal }}"
                                       min="0"
                                       max="{{ $landlordLimite > 0 ? $landlordLimite : 999999 }}"
                                       placeholder="0">
                                <span class="input-group-text text-muted">créditos/mês</span>
                            </div>
                            @error('limite_mensal')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                <strong>0</strong> = usa o limite do plano
                                @if($landlordLimite > 0)
                                    ({{ $landlordLimite }} créditos)
                                @else
                                    (sem limite)
                                @endif
                            </small>
                        </div>
                        <div class="mt-auto">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-save-line me-1"></i> Salvar limite
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Consumo do mês --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Consumo</h5>
                    <small class="text-muted">{{ $usage['month_label'] ?? $usage['month'] }}</small>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded text-center">
                                <p class="fs-20 fw-bold mb-0 text-primary">{{ $usage['total_queries'] }}</p>
                                <small class="text-muted">Consultas</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded text-center">
                                <p class="fs-20 fw-bold mb-0 text-warning">{{ $usage['total_credits'] }}</p>
                                <small class="text-muted">Créditos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded text-center">
                                <p class="fs-20 fw-bold mb-0 text-success">{{ $usage['total_results'] }}</p>
                                <small class="text-muted">Publicações</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded text-center">
                                <p class="fs-20 fw-bold mb-0 {{ $usage['total_errors'] > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ $usage['total_errors'] }}
                                </p>
                                <small class="text-muted">Erros</small>
                            </div>
                        </div>
                    </div>

                    @if($usage['limit'] > 0)
                    @php $pct = $usage['limit_percent']; @endphp
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-semibold">Uso do limite</small>
                            <small class="{{ $pct >= 90 ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $usage['total_credits'] }}/{{ $usage['limit'] }} ({{ $pct }}%)
                            </small>
                        </div>
                        <div class="progress mb-1" style="height:8px;border-radius:6px;">
                            <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success') }}"
                                 style="width:{{ min($pct,100) }}%;border-radius:6px;"></div>
                        </div>
                        @if($usage['limit_remaining'] !== null)
                            <small class="text-muted">
                                Restam <strong>{{ $usage['limit_remaining'] }}</strong> créditos
                            </small>
                        @endif
                        @if($pct >= 90)
                        <div class="alert alert-danger d-flex gap-2 align-items-center py-2 mt-2 mb-0">
                            <i class="ri-alert-line fs-14 flex-shrink-0"></i>
                            <small>Limite quase atingido. Novas consultas serão bloqueadas.</small>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center text-muted py-2">
                        <i class="ri-infinity-line fs-20 d-block mb-1 opacity-50"></i>
                        <small>Sem limite configurado</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Histórico --}}
        @if(count($history) > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Histórico de consumo</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mês</th>
                                    <th class="text-center">Consultas</th>
                                    <th class="text-center">Créditos usados</th>
                                    <th class="text-center">Publicações encontradas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $row)
                                <tr>
                                    <td class="fw-medium">{{ $row->month_label }}</td>
                                    <td class="text-center text-muted">{{ $row->total_queries }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-subtle text-warning">{{ $row->total_credits }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success">{{ $row->total_results }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

@endif

@endsection
