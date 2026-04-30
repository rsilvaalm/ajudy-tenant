@extends('layouts.app')
@section('title', 'Editar Perfil')
@section('page-title', 'Editar Perfil')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">{{ $profile->name }}</h5>
        <a href="{{ route('perfis.index') }}" class="btn btn-sm btn-light">
            <i class="ri-arrow-left-line me-1"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('perfis.update', $profile->id) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $profile->name) }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="description"
                           value="{{ old('description', $profile->description) }}"
                           class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label">Módulos com acesso</label>
                    @if($modules->isEmpty())
                        <div class="alert alert-warning py-2">Nenhum módulo ativo disponível.</div>
                    @else
                    <div class="row g-2">
                        @foreach($modules as $module)
                        <div class="col-md-4">
                            <div class="form-check card border {{ in_array($module->id, $selectedModules) ? 'border-primary border-2' : '' }} p-3 mb-0 h-100">
                                <input class="form-check-input" type="checkbox"
                                       name="module_ids[]"
                                       value="{{ $module->id }}"
                                       id="module_{{ $module->id }}"
                                       {{ in_array($module->id, old('module_ids', $selectedModules)) ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="module_{{ $module->id }}"
                                       style="cursor:pointer;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="{{ $module->icon ?? 'ri-apps-line' }} text-primary fs-16"></i>
                                        <span class="fw-medium">{{ $module->name }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Salvar alterações
                </button>
                <a href="{{ route('perfis.index') }}" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection
