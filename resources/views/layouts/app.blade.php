<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $currentTenant->name ?? 'Ajudy' }}</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/ajudy/favicon.png') }}">
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">

    @php
        $primary   = $customization->color_primary   ?? ($currentTenant->color_primary   ?? '#1a3c5e');
        $secondary = $customization->color_secondary ?? ($currentTenant->color_secondary ?? '#c8a84b');
        $tertiary  = $customization->color_tertiary  ?? ($currentTenant->color_tertiary  ?? '#f5f5f5');
        $logoVertical = $customization->logo_vertical ?? null;
    @endphp

    <style>
        :root {
            --brand-primary:   {{ $primary }};
            --brand-secondary: {{ $secondary }};
            --brand-tertiary:  {{ $tertiary }};
        }
        .btn-primary, .bg-primary { background-color: var(--brand-primary) !important; border-color: var(--brand-primary) !important; }
        .btn-primary:hover { opacity: .88; }
        .text-primary { color: var(--brand-primary) !important; }
        .page-item.active .page-link { background-color: var(--brand-primary) !important; border-color: var(--brand-primary) !important; }
        .page-link { color: var(--brand-primary); }
        .navbar-nav .nav-link.active { background-color: rgba(255,255,255,0.12) !important; }
        .table-responsive { overflow: visible !important; }
        .card { overflow: visible !important; }
    </style>
</head>
<body>

<div id="layout-wrapper">

    {{-- ── TOP BAR ──────────────────────────────────────────────────────── --}}
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex align-items-center">

                    {{-- Logo horizontal (modo collapsed) --}}
                    <div class="navbar-brand-box horizontal-logo">
                        <a href="{{ route('dashboard') }}" class="logo logo-dark">
                            <span class="logo-lg">
                                <img src="{{ asset('assets/images/ajudy/logo-vertical.png') }}" height="28">
                            </span>
                            <span class="logo-sm">
                                <img src="{{ asset('assets/images/ajudy/logo-sm.png') }}" height="22">
                            </span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="logo logo-light">
                            <span class="logo-lg">
                                <img src="{{ asset('assets/images/ajudy/logo-vertical.png') }}" height="28">
                            </span>
                            <span class="logo-sm">
                                <img src="{{ asset('assets/images/ajudy/logo-sm.png') }}" height="22">
                            </span>
                        </a>
                    </div>

                    {{-- Hamburguer --}}
                    <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon">
                        <span class="hamburger-icon"><span></span><span></span><span></span></span>
                    </button>

                </div>

                <div class="d-flex align-items-center gap-1">

                    {{-- Fullscreen --}}
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                            data-toggle="fullscreen">
                        <i class="bx bx-fullscreen fs-22"></i>
                    </button>

                    {{-- Modo noturno --}}
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class="bx bx-moon fs-22"></i>
                    </button>

                    {{-- Perfil --}}
                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <span class="rounded-circle header-profile-user bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                      style="width:36px;height:36px;font-size:15px;flex-shrink:0;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="text-start ms-xl-2">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">
                                        {{ \Illuminate\Support\Facades\DB::table('profile_user')
                                            ->join('profiles', 'profiles.id', '=', 'profile_user.profile_id')
                                            ->where('profile_user.user_id', auth()->id())
                                            ->value('profiles.name') ?? 'Usuário' }}
                                    </span>
                                </span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">
                                Olá, {{ explode(' ', auth()->user()->name)[0] }}!
                            </h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                Perfil
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i>
                                Bloquear tela
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="mdi mdi-logout fs-16 align-middle me-1"></i>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    {{-- ── SIDEBAR ──────────────────────────────────────────────────────── --}}
    <div class="app-menu navbar-menu">
        <div class="navbar-brand-box">

            {{-- Logo do tenant na sidebar --}}
            <a href="{{ route('dashboard') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ asset('assets/images/ajudy/logo-sm.png') }}" height="22">
                </span>
                <span class="logo-lg">
                    @if($logoVertical)
                        <img src="{{ $logoVertical }}" height="36" style="object-fit:contain;max-width:140px;">
                    @else
                        <img src="{{ asset('assets/images/ajudy/logo-vertical.png') }}" height="28">
                    @endif
                </span>
            </a>
            <a href="{{ route('dashboard') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ asset('assets/images/ajudy/logo-sm.png') }}" height="22">
                </span>
                <span class="logo-lg">
                    @if($logoVertical)
                        <img src="{{ $logoVertical }}" height="36" style="object-fit:contain;max-width:140px;filter:brightness(0) invert(1);">
                    @else
                        <img src="{{ asset('assets/images/ajudy/logo-vertical.png') }}" height="28">
                    @endif
                </span>
            </a>

            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        <div id="scrollbar">
            <div class="container-fluid">
                <ul class="navbar-nav" id="navbar-nav">

                    <li class="menu-title"><span>Menu</span></li>

                    {{-- Dashboard sempre visível --}}
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                           class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="ri-dashboard-2-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Módulos liberados para este tenant --}}
                    @php
                        $modules = \Illuminate\Support\Facades\DB::table('modules')
                            ->where('is_active', true)
                            ->orderBy('id')
                            ->get();
                    @endphp

                    @if($modules->isNotEmpty())
                    <li class="menu-title mt-2"><span>Módulos</span></li>

                    @foreach($modules as $module)
                    <li class="nav-item">
                        <a href="{{ isset($module->route_prefix) ? url($module->route_prefix) : '#' }}"
                           class="nav-link menu-link {{ request()->is(($module->route_prefix ?? '__') . '*') ? 'active' : '' }}">
                            <i class="{{ $module->icon ?? 'ri-apps-line' }}"></i>
                            <span>{{ $module->name }}</span>
                        </a>
                    </li>
                    @endforeach
                    @endif

                </ul>
            </div>
        </div>
    </div>

    {{-- ── CONTEÚDO ─────────────────────────────────────────────────────── --}}
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @hasSection('page-title')
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between"
                             style="background-color:var(--brand-primary);margin:-24px -24px 24px;padding:16px 24px;">
                            <h4 style="color:#fff;margin:0;">@yield('page-title')</h4>
                            @hasSection('page-actions')
                            <div>@yield('page-actions')</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @yield('content')

            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">{{ date('Y') }} &copy; {{ $currentTenant->name ?? 'Ajudy' }}</div>
                    <div class="col-sm-6 text-end">
                        <small class="text-muted">Powered by Ajudy</small>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</div>

{{-- ── Toast container ──────────────────────────────────────────────────── --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index:9999" id="toast-container"></div>

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    // Toast global
    function showToast(type, message) {
        const colors = {
            success: { bg: 'bg-success', icon: 'ri-checkbox-circle-line' },
            error:   { bg: 'bg-danger',  icon: 'ri-close-circle-line' },
            warning: { bg: 'bg-warning', icon: 'ri-alert-line' },
            info:    { bg: 'bg-info',    icon: 'ri-information-line' },
        };
        const c  = colors[type] || colors.info;
        const id = 'toast-' + Date.now();
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', `
            <div id="${id}" class="toast align-items-center text-white ${c.bg} border-0 show mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="${c.icon} fs-16"></i><span>${message}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`);
        setTimeout(() => document.getElementById(id)?.remove(), 4000);
    }

    // Exibe toasts de session vindos do controller
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif
    @if(session('warning'))
        showToast('warning', '{{ session('warning') }}');
    @endif
</script>

@stack('scripts')
</body>
</html>
