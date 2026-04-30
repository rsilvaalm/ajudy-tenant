@extends('layouts.app')
@section('title', 'Novo Usuário')
@section('page-title', 'Novo Usuário')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Dados do usuário</h5>
        <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-light">
            <i class="ri-arrow-left-line me-1"></i> Voltar
        </a>
    </div>
    <div class="card-body">

        <div class="d-flex align-items-center gap-2 p-2 rounded mb-4"
             style="background:rgba(41,156,219,.08);border:1px solid rgba(41,156,219,.2);">
            <i class="ri-mail-send-line text-info fs-16"></i>
            <small>Após o cadastro, um e-mail será enviado ao usuário com o link para cadastrar sua senha.</small>
        </div>

        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Nome completo">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="usuario@email.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Perfil <span class="text-danger">*</span></label>
                    <select name="profile_id"
                            class="form-select @error('profile_id') is-invalid @enderror">
                        <option value="">Selecione um perfil...</option>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}"
                                    {{ old('profile_id') == $profile->id ? 'selected' : '' }}>
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
                               {{ old('is_lawyer') ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="is_lawyer">
                            <i class="ri-scales-line me-1 text-primary"></i> É advogado
                        </label>
                    </div>
                </div>

                {{-- Campo OAB — visível só se is_lawyer marcado --}}
                <div class="col-md-6" id="oab-field"
                     style="{{ old('is_lawyer') ? '' : 'display:none;' }}">
                    <label class="form-label">
                        OAB <span class="text-danger">*</span>
                        <small class="text-muted fw-normal">— ex: 12345 / SP</small>
                    </label>
                    <input type="text" name="oab" value="{{ old('oab') }}"
                           class="form-control @error('oab') is-invalid @enderror"
                           placeholder="12345 / SP"
                           maxlength="20">
                    @error('oab')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-user-add-line me-1"></i> Criar e enviar convite
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
