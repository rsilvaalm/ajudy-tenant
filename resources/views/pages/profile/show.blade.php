@extends('layouts.app')
@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')

@section('content')

<div class="row g-4">

    {{-- Card: dados pessoais --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Dados Pessoais</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('perfil.update') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center gap-4 mb-4">
                        {{-- Avatar atual --}}
                        <div class="position-relative">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}"
                                     id="avatar-preview"
                                     class="rounded-circle"
                                     style="width:90px;height:90px;object-fit:cover;border:3px solid var(--brand-primary);">
                            @else
                                <div id="avatar-preview"
                                     class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                     style="width:90px;height:90px;font-size:32px;border:3px solid var(--brand-primary);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="btn btn-sm btn-light" for="avatar">
                                <i class="ri-upload-2-line me-1"></i> Alterar foto
                            </label>
                            <input type="file" name="avatar" id="avatar"
                                   class="d-none" accept="image/jpg,image/jpeg,image/png,image/webp"
                                   onchange="previewAvatar(this)">
                            @error('avatar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-12 mt-1">JPG, PNG ou WebP. Máx 2MB.</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control bg-light text-muted"
                                   value="{{ $user->email }}" disabled>
                            <small class="text-muted fs-11">O e-mail não pode ser alterado.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Perfil</label>
                            <input type="text" class="form-control bg-light text-muted"
                                   value="{{ $profile->profile_name ?? '—' }}" disabled>
                        </div>

                        @if($user->is_lawyer)
                        <div class="col-md-6">
                            <label class="form-label">
                                OAB
                                <small class="text-muted fw-normal">— ex: 12345 / SP</small>
                            </label>
                            <input type="text" name="oab"
                                   value="{{ old('oab', $user->oab) }}"
                                   class="form-control @error('oab') is-invalid @enderror"
                                   placeholder="12345 / SP"
                                   maxlength="20">
                            @error('oab')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Salvar dados
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Card: trocar senha --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Alterar Senha</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('perfil.password') }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group mb-3">
                        <label class="form-label">Senha atual <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input id="current_password" type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePass('current_password','ico1')"
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 text-muted"
                                    style="border:none;background:none;padding:0;">
                                <i class="ri-eye-line fs-16" id="ico1"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Nova senha <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input id="new_password" type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Mínimo 8 caracteres"
                                   oninput="checkStrength(this.value)">
                            <button type="button" onclick="togglePass('new_password','ico2')"
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 text-muted"
                                    style="border:none;background:none;padding:0;">
                                <i class="ri-eye-line fs-16" id="ico2"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        {{-- Indicador de força --}}
                        <div id="strength-wrapper" class="mt-2" style="display:none;">
                            <div class="progress" style="height:4px;">
                                <div id="strength-bar" class="progress-bar" style="width:0%;transition:width .3s;"></div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                <small id="req-length"  class="text-muted fs-11"><i class="ri-close-circle-line me-1"></i>8+</small>
                                <small id="req-upper"   class="text-muted fs-11"><i class="ri-close-circle-line me-1"></i>A-Z</small>
                                <small id="req-lower"   class="text-muted fs-11"><i class="ri-close-circle-line me-1"></i>a-z</small>
                                <small id="req-number"  class="text-muted fs-11"><i class="ri-close-circle-line me-1"></i>0-9</small>
                                <small id="req-special" class="text-muted fs-11"><i class="ri-close-circle-line me-1"></i>@#!</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Confirmar nova senha <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input id="confirm_password" type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repita a nova senha">
                            <button type="button" onclick="togglePass('confirm_password','ico3')"
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 text-muted"
                                    style="border:none;background:none;padding:0;">
                                <i class="ri-eye-line fs-16" id="ico3"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-lock-password-line me-1"></i> Alterar senha
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('ri-eye-line');
        icon.classList.toggle('ri-eye-off-line');
    }

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('avatar-preview');
                // Se for div (inicial), substitui por img
                if (preview.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.id        = 'avatar-preview';
                    img.className = 'rounded-circle';
                    img.style     = 'width:90px;height:90px;object-fit:cover;border:3px solid var(--brand-primary);';
                    img.src       = e.target.result;
                    preview.replaceWith(img);
                } else {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function checkStrength(val) {
        document.getElementById('strength-wrapper').style.display = val.length ? 'block' : 'none';
        const checks = {
            length:  val.length >= 8,
            upper:   /[A-Z]/.test(val),
            lower:   /[a-z]/.test(val),
            number:  /\d/.test(val),
            special: /[@$!%*?&#]/.test(val),
        };
        Object.keys(checks).forEach(k => {
            const el = document.getElementById('req-' + k);
            el.className = checks[k] ? 'text-success fs-11' : 'text-muted fs-11';
            el.querySelector('i').className = checks[k]
                ? 'ri-checkbox-circle-line me-1'
                : 'ri-close-circle-line me-1';
        });
        const score = Object.values(checks).filter(Boolean).length;
        const bar = document.getElementById('strength-bar');
        bar.style.width = (score / 5 * 100) + '%';
        bar.className = 'progress-bar ' + ['','bg-danger','bg-danger','bg-warning','bg-info','bg-success'][score];
    }
</script>
@endpush

@endsection
