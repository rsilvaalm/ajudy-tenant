@extends('layouts.app')
@section('title', 'Configurações de Publicações')
@section('page-title', 'Publicações')

@section('content')

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
                        <p class="fw-semibold mb-1">Publicações habilitadas</p>
                        <p class="text-muted fs-13 mb-0">
                            A aba <strong>Publicações</strong> está disponível na ficha de cada processo.
                        </p>
                    @else
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle bg-light text-muted fs-36">
                                <i class="ri-newspaper-line"></i>
                            </div>
                        </div>
                        <p class="fw-semibold mb-1 text-muted">Publicações desabilitadas</p>
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
                                <i class="ri-pause-circle-line me-1"></i> Desabilitar publicações
                            @else
                                <i class="ri-play-circle-line me-1"></i> Habilitar publicações
                            @endif
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Consumo do mês --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Consumo — {{ $usage['month_label'] ?? $usage['month'] }}</h5>
                <small class="text-muted">Mês atual</small>
            </div>
            <div class="card-body">

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <p class="fs-22 fw-bold mb-0 text-primary">{{ $usage['total_queries'] }}</p>
                            <small class="text-muted">Consultas</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <p class="fs-22 fw-bold mb-0 text-warning">{{ $usage['total_credits'] }}</p>
                            <small class="text-muted">Créditos usados</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <p class="fs-22 fw-bold mb-0 text-success">{{ $usage['total_results'] }}</p>
                            <small class="text-muted">Publicações</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <p class="fs-22 fw-bold mb-0 {{ $usage['total_errors'] > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $usage['total_errors'] }}
                            </p>
                            <small class="text-muted">Erros</small>
                        </div>
                    </div>
                </div>

                {{-- Barra de limite --}}
                @if($usage['limit'] > 0)
                @php $pct = $usage['limit_percent']; @endphp
                <div class="mb-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-semibold">Uso do limite mensal</small>
                        <small class="{{ $pct >= 90 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $usage['total_credits'] }} / {{ $usage['limit'] }} créditos ({{ $pct }}%)
                        </small>
                    </div>
                    <div class="progress mb-1" style="height:10px;border-radius:6px;">
                        <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success') }}"
                             style="width:{{ min($pct,100) }}%;border-radius:6px;"></div>
                    </div>
                    @if($usage['limit_remaining'] !== null)
                    <small class="text-muted">
                        Restam <strong>{{ $usage['limit_remaining'] }}</strong> créditos este mês.
                    </small>
                    @endif
                </div>

                @if($pct >= 90)
                <div class="alert alert-danger d-flex gap-2 align-items-center py-2 mt-3">
                    <i class="ri-alert-line fs-16 flex-shrink-0"></i>
                    <small>Limite quase atingido. Novas consultas serão bloqueadas ao atingir o limite. Contate o suporte para aumentar.</small>
                </div>
                @endif

                @elseif($limiteMensal == 0)
                <div class="p-3 border rounded text-center text-muted">
                    <i class="ri-infinity-line fs-20 d-block mb-1 opacity-50"></i>
                    <small>Sem limite de créditos configurado.</small>
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

@endsection
