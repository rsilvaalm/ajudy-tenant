@extends('layouts.auth')

@section('title', 'Cadastrar Senha')

@section('content')

<h4 class="mb-1">Cadastre sua senha</h4>
<p class="subtitle">Crie uma senha segura para acessar o sistema.</p>

@if($errors->any())
<div class="alert alert-danger py-2 mb-3 w-100" style="font-size:13px;border-radius:8px;">
    <i class="ri-error-warning-line me-1"></i>{{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('set-password.store') }}" style="width:100%;">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="form-group">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control bg-light text-muted"
               value="{{ $email }}" disabled>
    </div>

    <div class="form-group">
        <label class="form-label">Nova Senha <span class="text-danger">*</span></label>
        <div class="position-relative">
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Mínimo 8 caracteres"
                   oninput="checkStrength(this.value)">
            <button type="button" onclick="togglePass('password','icon1')"
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 text-muted"
                    style="border:none;background:none;padding:0;">
                <i class="ri-eye-line fs-16" id="icon1"></i>
            </button>
        </div>
        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

        <div class="mt-2" id="strength-wrapper" style="display:none;">
            <div class="progress" style="height:4px;">
                <div id="strength-bar" class="progress-bar" style="width:0%;transition:width .3s;"></div>
            </div>
            <div class="d-flex gap-3 mt-2 flex-wrap">
                <small id="req-length"  class="text-muted"><i class="ri-close-circle-line me-1"></i>8+ caracteres</small>
                <small id="req-upper"   class="text-muted"><i class="ri-close-circle-line me-1"></i>Maiúscula</small>
                <small id="req-lower"   class="text-muted"><i class="ri-close-circle-line me-1"></i>Minúscula</small>
                <small id="req-number"  class="text-muted"><i class="ri-close-circle-line me-1"></i>Número</small>
                <small id="req-special" class="text-muted"><i class="ri-close-circle-line me-1"></i>Especial (@$!%*?&#)</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
        <div class="position-relative">
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control" placeholder="Repita a senha">
            <button type="button" onclick="togglePass('password_confirmation','icon2')"
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 text-muted"
                    style="border:none;background:none;padding:0;">
                <i class="ri-eye-line fs-16" id="icon2"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-login mt-2">
        <i class="ri-lock-unlock-line me-1"></i> Cadastrar senha e entrar
    </button>
</form>

@push('scripts')
<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('ri-eye-line');
        icon.classList.toggle('ri-eye-off-line');
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
            const ok = checks[k];
            el.className = ok ? 'text-success' : 'text-muted';
            el.querySelector('i').className = ok
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
