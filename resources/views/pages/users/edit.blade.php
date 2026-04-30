@extends('layouts.app')
@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">{{ $user->name }}</h5>
        <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-light">
            <i class="ri-arrow-left-line me-1"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('usuarios.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Perfil <span class="text-danger">*</span></label>
                    <select name="profile_id"
                            class="form-select @error('profile_id') is-invalid @enderror">
                        <option value="">Selecione um perfil...</option>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}"
                                    {{ old('profile_id', $currentProfileId) == $profile->id ? 'selected' : '' }}>
                                {{ $profile->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('profile_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Flag Advogado --}}
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox"
                               name="is_lawyer" id="is_lawyer" value="1"
                               onchange="toggleOab(this.checked)"
                               {{ old('is_lawyer', $user->is_lawyer) ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="is_lawyer">
                            <i class="ri-scales-line me-1 text-primary"></i> É advogado
                        </label>
                    </div>
                </div>

                {{-- Campo OAB --}}
                <div class="col-md-6" id="oab-field"
                     style="{{ old('is_lawyer', $user->is_lawyer) ? '' : 'display:none;' }}">
                    <label class="form-label">
                        OAB <span class="text-danger">*</span>
                        <small class="text-muted fw-normal">— ex: 12345 / SP</small>
                    </label>
                    <input type="text" name="oab" value="{{ old('oab', $user->oab) }}"
                           class="form-control @error('oab') is-invalid @enderror"
                           placeholder="12345 / SP"
                           maxlength="20">
                    @error('oab')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox"
                               name="is_active" id="is_active" value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Usuário ativo</label>
                    </div>
                </div>

            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleOab(checked) {
        const field = document.getElementById('oab-field');
        field.style.display = checked ? 'block' : 'none';
        if (!checked) {
            field.querySelector('input').value = '';
        }
    }
</script>
@endpush

@endsection
