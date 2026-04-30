@extends('layouts.app')
@section('title', 'Campos Personalizados')
@section('page-title', 'Campos Personalizados de Clientes')

@section('content')

<div class="row g-4">

    {{-- Formulário de novo campo --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Novo Campo</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('clientes.campos.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nome do campo <span class="text-danger">*</span></label>
                        <input type="text" name="label" value="{{ old('label') }}"
                               class="form-control @error('label') is-invalid @enderror"
                               placeholder="Ex: Número do processo INSS">
                        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="order" value="{{ old('order', 0) }}"
                               class="form-control" min="0">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox"
                               name="required" id="required" value="1"
                               {{ old('required') ? 'checked' : '' }}>
                        <label class="form-check-label" for="required">Campo obrigatório</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-add-line me-1"></i> Adicionar campo
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Lista de campos --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Campos cadastrados</h5>
            </div>
            <div class="card-body p-0" style="min-height:200px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ordem</th>
                                <th>Nome</th>
                                <th>Chave</th>
                                <th>Obrigatório</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fields as $field)
                            <tr>
                                <td class="text-muted">{{ $field->order }}</td>
                                <td class="fw-medium">{{ $field->label }}</td>
                                <td><code class="fs-11">{{ $field->key }}</code></td>
                                <td>
                                    @if($field->required)
                                        <span class="badge bg-danger-subtle text-danger">Sim</span>
                                    @else
                                        <span class="badge bg-light text-muted">Não</span>
                                    @endif
                                </td>
                                <td>
                                    @if($field->is_active)
                                        <span class="badge bg-success-subtle text-success">Ativo</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inativo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form method="POST"
                                          action="{{ route('clientes.campos.toggle', $field->id) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm {{ $field->is_active ? 'btn-soft-warning' : 'btn-soft-success' }}"
                                                title="{{ $field->is_active ? 'Desativar' : 'Ativar' }}">
                                            <i class="ri-{{ $field->is_active ? 'pause' : 'play' }}-circle-line"></i>
                                        </button>
                                    </form>
                                    <button onclick="confirmDelete(
                                                '{{ route('clientes.campos.destroy', $field->id) }}',
                                                'Remover o campo {{ addslashes($field->label) }}? Os valores já preenchidos serão perdidos.')"
                                            class="btn btn-sm btn-soft-danger ms-1">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="ri-list-check fs-36 d-block mb-2 opacity-25"></i>
                                    Nenhum campo personalizado criado ainda.
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

@endsection
