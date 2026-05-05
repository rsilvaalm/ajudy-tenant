@extends('layouts.app')
@section('title', 'Editar Processo')
@section('page-title', 'Editar Processo')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">{{ $process->number }}</h5>
        <a href="{{ route('processos.show', $process->id) }}" class="btn btn-sm btn-light">
            <i class="ri-arrow-left-line me-1"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('processos.update', $process->id) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <input type="text" class="form-control bg-light" value="{{ $client->name ?? '—' }}" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Pasta</label>
                    <select name="folder_id" class="form-select">
                        <option value="">Selecione...</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}"
                                    {{ old('folder_id', $process->folder_id) == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Data <span class="text-danger">*</span></label>
                    <input type="text" name="date"
                           value="{{ old('date', $process->date ? \Carbon\Carbon::parse($process->date)->format('Y-m-d') : '') }}"
                           class="form-control flatpickr-date @error('date') is-invalid @enderror"
                           placeholder="DD/MM/AAAA" autocomplete="off">
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Número do processo <span class="text-danger">*</span></label>
                    <input type="text" name="number" value="{{ old('number', $process->number) }}"
                           class="form-control @error('number') is-invalid @enderror">
                    @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Local do processo</label>
                    <input type="text" name="location" value="{{ old('location', $process->location) }}"
                           class="form-control uppercase">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Polo ativo <span class="text-danger">*</span></label>
                    <input type="text" name="active_pole" value="{{ old('active_pole', $process->active_pole) }}"
                           class="form-control uppercase @error('active_pole') is-invalid @enderror">
                    @error('active_pole')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Polo passivo <span class="text-danger">*</span></label>
                    <input type="text" name="passive_pole" value="{{ old('passive_pole', $process->passive_pole) }}"
                           class="form-control uppercase @error('passive_pole') is-invalid @enderror">
                    @error('passive_pole')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Salvar alterações
                </button>
                <button type="button"
                        onclick="confirmDelete('{{ route('processos.destroy', $process->id) }}', 'Remover o processo {{ addslashes($process->number) }}?')"
                        class="btn btn-soft-danger">
                    <i class="ri-delete-bin-line me-1"></i> Remover
                </button>
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
});
</script>
@endpush

@endsection
