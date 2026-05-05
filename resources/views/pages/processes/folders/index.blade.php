@extends('layouts.app')
@section('title', 'Pastas')
@section('page-title', 'Pastas de Processo')

@section('content')

<div class="row g-4">

    {{-- Formulário novo --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Nova Pasta</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('processos.pastas.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control uppercase @error('name') is-invalid @enderror"
                               placeholder="EX: TRABALHISTA">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cor</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" id="colorPicker"
                                   value="{{ old('color', '#1a3c5e') }}"
                                   class="form-control form-control-color" style="width:50px;height:38px;">
                            <input type="text" id="colorText" value="{{ old('color', '#1a3c5e') }}"
                                   class="form-control form-control-sm" maxlength="7"
                                   oninput="document.getElementById('colorPicker').value=this.value">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-add-line me-1"></i> Adicionar pasta
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Listagem --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Pastas cadastradas</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:14px;"></th>
                                <th>Nome</th>
                                <th>Processos</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($folders as $folder)
                            <tr>
                                <td>
                                    <div class="rounded-circle" style="width:14px;height:14px;background:{{ $folder->color ?? '#1a3c5e' }};"></div>
                                </td>
                                <td class="fw-medium">{{ $folder->name }}</td>
                                <td class="text-muted">
                                    {{ DB::table('processes')->where('folder_id', $folder->id)->whereNull('deleted_at')->count() }}
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-soft-primary me-1"
                                            onclick="editFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}', '{{ $folder->color }}')">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button onclick="confirmDelete('{{ route('processos.pastas.destroy', $folder->id) }}', 'Remover a pasta {{ addslashes($folder->name) }}?')"
                                            class="btn btn-sm btn-soft-danger">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="ri-folder-line fs-36 d-block mb-2 opacity-25"></i>
                                    Nenhuma pasta cadastrada ainda.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal edição --}}
<div class="modal fade" id="editFolderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Pasta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFolderForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_folder_name" class="form-control uppercase">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cor</label>
                        <input type="color" name="color" id="edit_folder_color"
                               class="form-control form-control-color" style="width:50px;height:38px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.uppercase').forEach(el => {
        el.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    });

    document.getElementById('colorPicker')?.addEventListener('input', function () {
        document.getElementById('colorText').value = this.value;
    });

    function editFolder(id, name, color) {
        document.getElementById('editFolderForm').action = `/processos/pastas/${id}`;
        document.getElementById('edit_folder_name').value  = name;
        document.getElementById('edit_folder_color').value = color;
        new bootstrap.Modal(document.getElementById('editFolderModal')).show();
    }

    // Maiúsculas no modal de edição
    document.getElementById('edit_folder_name')?.addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
</script>
@endpush

@endsection
