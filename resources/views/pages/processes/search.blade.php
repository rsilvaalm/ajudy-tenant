@extends('layouts.app')
@section('title', 'Pesquisar Processo')
@section('page-title', 'Pesquisar Processo')

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('processos.buscar') }}" id="search-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Número do processo</label>
                    <input type="text" name="number" value="{{ $number }}"
                           class="form-control" placeholder="Ex: 1004017-79.2023.4.01.3302">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data</label>
                    <input type="text" name="date" value="{{ $date }}"
                           class="form-control flatpickr-date"
                           placeholder="DD/MM/AAAA" autocomplete="off">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Polo passivo</label>
                    <input type="text" name="passive_pole" value="{{ $passivePole }}"
                           class="form-control" placeholder="Nome do polo passivo">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pasta</label>
                    <select name="folder_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" {{ $folderId == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cliente</label>
                    <input type="text" name="client" value="{{ $client ?? '' }}"
                           class="form-control" placeholder="Nome do cliente">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="ri-search-line me-1"></i> Pesquisar
                    </button>
                    <a href="{{ route('processos.buscar') }}" class="btn btn-light px-3" title="Limpar filtros">
                        <i class="ri-eraser-line"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if($hasFilter)
    @if($processes->isEmpty())
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="ri-information-line fs-18"></i>
        Nenhum processo encontrado com os filtros informados.
    </div>
    @else
    <div class="mb-2 text-muted fs-13">{{ $processes->count() }} processo(s) encontrado(s)</div>
    <div class="row g-3">
        @foreach($processes as $process)
        <div class="col-md-4">
            <div class="card h-100 border">
                <div class="card-body pb-2">
                    <div class="mb-2">
                        @if($process->folder_name)
                            <span class="badge fs-11" style="background:{{ $process->folder_color ?? '#1a3c5e' }};color:#fff;">
                                <i class="ri-folder-line me-1"></i>{{ $process->folder_name }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted fs-11">Sem pasta</span>
                        @endif
                    </div>
                    <p class="fw-semibold fs-13 mb-2 text-truncate" title="{{ $process->number }}">
                        {{ $process->number }}
                    </p>
                    <div class="fs-12 text-muted">
                        <div class="mb-1"><span class="fw-medium text-dark">Cliente:</span>
                            <a href="{{ route('clientes.show', $process->client_id) }}" class="text-primary">{{ $process->client_name }}</a>
                        </div>
                        <div class="mb-1"><span class="fw-medium text-dark">Data:</span>
                            {{ \Carbon\Carbon::parse($process->date)->format('d/m/Y') }}
                        </div>
                        <div class="mb-1"><span class="fw-medium text-dark">Polo ativo:</span> {{ $process->active_pole }}</div>
                        <div class="mb-1"><span class="fw-medium text-dark">Polo passivo:</span> {{ $process->passive_pole }}</div>
                        @if($process->location)
                        <div><span class="fw-medium text-dark">Local:</span> {{ $process->location }}</div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top pt-2 pb-2">
                    <a href="{{ route('processos.show', $process->id) }}" class="btn btn-sm btn-primary w-100">
                        <i class="ri-eye-line me-1"></i> Ver processo
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
@else
<div class="text-center text-muted py-5">
    <i class="ri-search-2-line fs-48 d-block mb-2 opacity-25"></i>
    <p>Informe pelo menos um filtro para pesquisar.</p>
</div>
@endif

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/pt.js"></script>
<script>
    flatpickr('.flatpickr-date', {
        locale: 'pt', dateFormat: 'Y-m-d',
        altInput: true, altFormat: 'd/m/Y',
        allowInput: true,
        defaultDate: '{{ $date }}' || null,
    });
</script>
@endpush

@endsection
