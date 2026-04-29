<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — {{ $currentTenant->name ?? 'Ajudy' }}</title>

    <link rel="icon" href="{{ asset('assets/images/ajudy/favicon.png') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">

    @php
        $primary   = $customization->color_primary   ?? ($currentTenant->color_primary   ?? '#4B7BE5');
        $secondary = $customization->color_secondary ?? ($currentTenant->color_secondary ?? '#7B5EA7');
        $tertiary  = $customization->color_tertiary  ?? ($currentTenant->color_tertiary  ?? '#F5F5F5');
        $logoPrimary = $customization->logo_primary  ?? null;
    @endphp

    <style>
        :root {
            --brand-primary:   {{ $primary }};
            --brand-secondary: {{ $secondary }};
            --brand-tertiary:  {{ $tertiary }};
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand-secondary) 0%, var(--brand-primary) 100%);
            padding: 20px;
        }

        .auth-card {
            display: flex;
            width: 100%;
            max-width: 860px;
            min-height: 480px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
        }

        /* Lado esquerdo — degradê com logo da Ajudy */
        .auth-left {
            width: 42%;
            background: linear-gradient(160deg, var(--brand-secondary) 0%, var(--brand-primary) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 40px 32px;
        }

        .auth-left .logo-ajudy {
            width: 140px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: .9;
        }

        .auth-left .logo-tenant {
            width: 100%;
            object-fit: contain;
            border-radius: 16px;
        }

        .auth-left .tenant-name {
            color: rgba(255,255,255,.75);
            font-size: 13px;
            text-align: center;
            margin-top: 8px;
        }

        /* Lado direito — formulário */
        .auth-right {
            width: 58%;
            background: var(--brand-tertiary, #F5F5F5);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }

        .auth-right h4 {
            font-size: 22px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .auth-right .subtitle {
            color: #718096;
            font-size: 14px;
            margin-bottom: 32px;
        }

        .auth-right .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #4a5568;
        }

        .auth-right .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            font-size: 14px;
            background: #fff;
        }

        .auth-right .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-primary) 15%, transparent);
        }

        .btn-login {
            width: 100%;
            padding: 11px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .3px;
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
            color: #fff;
            transition: opacity .2s;
        }

        .btn-login:hover, .btn-login:focus {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: #fff !important;
            opacity: .88;
        }

        .form-group { margin-bottom: 18px; }

        @media (max-width: 640px) {
            .auth-left  { display: none; }
            .auth-right { width: 100%; border-radius: 16px; }
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        {{-- Lado esquerdo --}}
        <div class="auth-left">
            {{-- Logo Ajudy (topo) --}}
            <img src="{{ asset('assets/images/ajudy/logo-vertical.png') }}"
                 alt="Ajudy"
                 class="logo-ajudy">

            {{-- Logo do tenant (centro) --}}
            <div class="text-center">
                @if($logoPrimary)
                    <img src="{{ $logoPrimary }}" alt="{{ $currentTenant->name ?? '' }}" class="logo-tenant">
                @else
                    <div style="width:160px;height:80px;background:rgba(255,255,255,.15);border-radius:8px;"></div>
                @endif

            </div>

            {{-- Espaço inferior --}}
            <div></div>
        </div>

        {{-- Lado direito --}}
        <div class="auth-right">
            @yield('content')
        </div>

    </div>
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
