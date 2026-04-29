@extends('layouts.auth')

@section('title', 'Entrar')

@section('content')

<h4>Bem-vindo(a) ao Ajudy</h4>
<p class="subtitle">Acesse sua conta para continuar</p>

@if($errors->any())
<div class="alert alert-danger py-2 mb-3 w-100" style="font-size:13px;border-radius:8px;">
    <i class="ri-error-warning-line me-1"></i>
    {{ $errors->first() }}
</div>
@endif

@if(session('status'))
<div class="alert alert-success py-2 mb-3 w-100" style="font-size:13px;border-radius:8px;">
    {{ session('status') }}
</div>
@endif

<form method="POST" action="{{ route('login.store') }}" style="width:100%;">
    @csrf

    <div class="form-group">
        <label class="form-label" for="email">E-mail</label>
        <input id="email"
               type="email"
               name="email"
               value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="seu@email.com"
               autocomplete="email"
               autofocus>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Senha</label>
        <div class="position-relative">
            <input id="password"
                   type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••"
                   autocomplete="current-password">
            <button type="button"
                    onclick="togglePassword()"
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 text-muted"
                    style="border:none;background:none;padding:0;">
                <i class="ri-eye-line fs-16" id="toggleIcon"></i>
            </button>
        </div>
        @error('password')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label text-muted" for="remember" style="font-size:13px;">
                Lembrar de mim
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-login">
        Entrar
    </button>

</form>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
        } else {
            input.type = 'password';
            icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
        }
    }
</script>
@endpush

@endsection
