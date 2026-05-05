@extends('layouts.app')
@section('title', 'Novo Processo')
@section('page-title', 'Novo Processo')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Dados do processo</h5>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-light">
            <i class="ri-arrow-left-line me-1"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('processos.store') }}">
            @csrf
            <div class="row g-3">

                {{-- Cliente --}}
                <div class="col-md-6">
                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                    @if($client)
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="text" class="form-control bg-light" value="{{ $client->name }}" disabled>
                    @else
                        <div class="position-relative">
                            <input type="text" id="process_client_search"
                                   class="form-control @error('client_id') is-invalid @enderror"
                                   placeholder="Digite o nome do cliente..." autocomplete="off">
                            <input type="hidden" name="client_id" id="process_client_id">
                            <div id="process_client_results"
                                 class="position-absolute w-100 bg-white border rounded shadow-lg"
                                 style="top:100%;z-index:999;display:none;max-height:200px;overflow-y:auto;"></div>
                        </div>
                        <div id="process_client_selected" class="mt-1 d-none">
                            <span class="badge bg-primary-subtle text-primary fs-12" id="process_client_name"></span>
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1" onclick="clearProcessClient()">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        @error('client_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    @endif
                </div>

                {{-- Pasta --}}
                <div class="col-md-3">
                    <label class="form-label">Pasta</label>
                    <select name="folder_id" class="form-select">
                        <option value="">Selecione...</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" {{ old('folder_id') == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Data --}}
                <div class="col-md-3">
                    <label class="form-label">Data <span class="text-danger">*</span></label>
                    <input type="text" name="date" value="{{ old('date') }}"
                           class="form-control flatpickr-date @error('date') is-invalid @enderror"
                           placeholder="DD/MM/AAAA" autocomplete="off">
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Número --}}
                <div class="col-md-6">
                    <label class="form-label">Número do processo <span class="text-danger">*</span></label>
                    <input type="text" name="number" value="{{ old('number') }}"
                           class="form-control @error('number') is-invalid @enderror"
                           placeholder="Ex: 1004017-79.2023.4.01.3302">
                    @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Local --}}
                <div class="col-md-6">
                    <label class="form-label">Local do processo</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                           class="form-control uppercase" placeholder="EX: 1ª VARA DO TRABALHO">
                </div>

                {{-- Polo ativo --}}
                <div class="col-md-6">
                    <label class="form-label">Polo ativo <span class="text-danger">*</span></label>
                    <input type="text" name="active_pole"
                           value="{{ old('active_pole', $client->name ?? '') }}"
                           class="form-control uppercase @error('active_pole') is-invalid @enderror"
                           placeholder="POLO ATIVO">
                    @error('active_pole')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Polo passivo --}}
                <div class="col-md-6">
                    <label class="form-label">Polo passivo <span class="text-danger">*</span></label>
                    <input type="text" name="passive_pole" value="{{ old('passive_pole') }}"
                           class="form-control uppercase @error('passive_pole') is-invalid @enderror"
                           placeholder="POLO PASSIVO">
                    @error('passive_pole')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Cadastrar processo
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/pt.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    flatpickr('.flatpickr-date', {
        locale: 'pt', dateFormat: 'Y-m-d',
        altInput: true, altFormat: 'd/m/Y', allowInput: true,
    });

    document.querySelectorAll('.uppercase').forEach(el => {
        el.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    });

    let t = null;
    const si = document.getElementById('process_client_search');
    if (si) {
        si.addEventListener('input', function () {
            clearTimeout(t);
            const q = this.value.trim();
            if (q.length < 2) { document.getElementById('process_client_results').style.display = 'none'; return; }
            t = setTimeout(() => searchClient(q), 300);
        });
    }

    function searchClient(q) {
        fetch(`/atendimento/buscar-clientes?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(clients => {
            const c = document.getElementById('process_client_results');
            c.innerHTML = !clients.length
                ? '<div class="p-3 text-muted text-center fs-13">Nenhum cliente encontrado.</div>'
                : clients.map(cl => `
                    <div class="p-2 px-3 border-bottom" style="cursor:pointer;"
                         onclick="selectProcessClient(${cl.id}, '${cl.name.replace(/'/g,"\\'")}')">
                        <div class="fw-medium fs-13">${cl.name}</div>
                        <small class="text-muted">${cl.cpf || ''}</small>
                    </div>`).join('');
            c.style.display = 'block';
        });
    }
});

function selectProcessClient(id, name) {
    document.getElementById('process_client_id').value = id;
    document.getElementById('process_client_search').value = '';
    document.getElementById('process_client_results').style.display = 'none';
    document.getElementById('process_client_name').textContent = name;
    document.getElementById('process_client_selected').classList.remove('d-none');
    const pole = document.querySelector('[name=active_pole]');
    if (pole && !pole.value) pole.value = name;
}

function clearProcessClient() {
    document.getElementById('process_client_id').value = '';
    document.getElementById('process_client_selected').classList.add('d-none');
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('#process_client_search') && !e.target.closest('#process_client_results')) {
        const r = document.getElementById('process_client_results');
        if (r) r.style.display = 'none';
    }
});
</script>
@endpush

@endsection
