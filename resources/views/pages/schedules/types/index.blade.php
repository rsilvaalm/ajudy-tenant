@extends('layouts.app')
@section('title', 'Tipos de Agendamento')
@section('page-title', 'Tipos de Agendamento')

@section('content')

<div class="row g-4">

    {{-- Formulário --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Novo Tipo</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('agendamentos.tipos.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control uppercase @error('name') is-invalid @enderror"
                               placeholder="EX: AUDIÊNCIA">
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
                        <i class="ri-add-line me-1"></i> Adicionar tipo
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Listagem --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Tipos cadastrados</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:14px;"></th>
                                <th>Nome</th>
                                <th>Agendamentos</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $type)
                            <tr>
                                <td><div class="rounded-circle" style="width:14px;height:14px;background:{{ $type->color }};"></div></td>
                                <td class="fw-medium">{{ $type->name }}</td>
                                <td class="text-muted">
                                    {{ DB::table('schedules')->where('schedule_type_id', $type->id)->whereNull('deleted_at')->count() }}
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-soft-primary me-1"
                                            onclick="editType({{ $type->id }}, '{{ addslashes($type->name) }}', '{{ $type->color }}')">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button onclick="confirmDelete('{{ route('agendamentos.tipos.destroy', $type->id) }}', 'Remover o tipo {{ addslashes($type->name) }}?')"
                                            class="btn btn-sm btn-soft-danger">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="ri-calendar-check-line fs-36 d-block mb-2 opacity-25"></i>
                                    Nenhum tipo cadastrado ainda.
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
<div class="modal fade" id="editTypeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Tipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTypeForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_type_name" class="form-control uppercase">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cor</label>
                        <input type="color" name="color" id="edit_type_color"
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

    function editType(id, name, color) {
        document.getElementById('editTypeForm').action = `/agendamentos/tipos/${id}`;
        document.getElementById('edit_type_name').value  = name;
        document.getElementById('edit_type_color').value = color;
        new bootstrap.Modal(document.getElementById('editTypeModal')).show();
    }

    document.getElementById('edit_type_name')?.addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
</script>
@endpush

@endsection