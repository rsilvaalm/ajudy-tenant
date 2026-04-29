@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="row justify-content-center">
    <div class="col-xxl-10">

        {{-- Boas-vindas --}}
        <div class="card" style="border:none;background:transparent;box-shadow:none;">
            <div class="card-body py-5 text-center">

                @php
                    $hour = now()->hour;
                    $greeting = match(true) {
                        $hour >= 5  && $hour < 12 => 'Bom dia',
                        $hour >= 12 && $hour < 18 => 'Boa tarde',
                        default                   => 'Boa noite',
                    };
                @endphp

                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title rounded-circle fs-36 text-white fw-bold"
                         style="background-color:var(--brand-primary);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>

                <h3 class="fw-semibold mb-1">
                    {{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}!
                </h3>
                <p class="text-muted fs-15 mb-0">
                    Bem-vindo ao sistema. Utilize o menu lateral para navegar.
                </p>

            </div>
        </div>

    </div>
</div>

@endsection
