@extends('layouts.auth')

@section('title', 'Tela Bloqueada')

@section('content')

@php
    $authUser    = auth()->user();
    $userName    = $authUser?->name ?? 'Usuário';
    $userInitial = strtoupper(substr($userName, 0, 1));
    $userAvatar  = $authUser?->avatar ?? null;
@endphp

<div class="text-center mb-4">
    {{-- Avatar --}}
    <div class="avatar-lg mx-auto mb-3">
        @if($userAvatar)
            <img src="{{ asset($userAvatar) }}"
                 class="rounded-circle"
                 style="width:72px;height:72px;object-fit:cover;">
        @else
            <div class="avatar-title rounded-circle fs-36 text-white fw-bold"
                 style="background-color:var(--brand-primary);">
                {{ $userInitial }}
            </div>
        @endif
    </div>
    <h5 class="fw-semibold mb-0">{{ $userName }}</h5>
    <p class="text-muted fs-13 mb-0">Tela bloqueada</p>
</div>

@if($errors->any())
<div class="alert alert-danger py-2 mb-3 w-100" style="font-size:13px;border-radius:8px;">
    <i class="ri-error-warning-line me-1"></i>
    {{ $errors->first() }}
</div>
@endif

{{-- Formulário de desbloqueio --}}
<form method="POST" action="{{ route('lock.unlock') }}" style="width:100%;">
    @csrf

    <div class="form-group mb-4">
        <label class="form-label" for="password">Digite sua senha para continuar</label>
        <div class="position-relative">
            <input id="password"
                   type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••"
                   autocomplete="current-password"
                   autofocus>
            <button type="button"
                    onclick="toggleLockPassword()"
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 text-muted"
                    style="border:none;background:none;padding:0;">
                <i class="ri-eye-line fs-16" id="lockToggleIcon"></i>
            </button>
        </div>
        @error('password')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-login mb-3">
        <i class="ri-lock-unlock-line me-1"></i> Desbloquear
    </button>

</form>

{{-- Logout fora do form de unlock — forms aninhados não funcionam em HTML --}}
<div class="text-center mt-1">
    <form method="POST" action="{{ route('lock.logout') }}">
        @csrf
        <button type="submit" class="btn btn-link text-muted fs-13 p-0">
            <i class="ri-user-line me-1"></i> Entrar com outra conta
        </button>
    </form>
</div>

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, private">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@push('scripts')
<script>
    function toggleLockPassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('lockToggleIcon');
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