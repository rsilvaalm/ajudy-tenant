<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="sm" data-sidebar-image="none" data-preloader="disable">
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
        $primary         = $customization->color_primary   ?? ($currentTenant->color_primary   ?? '#1a3c5e');
        $secondary       = $customization->color_secondary ?? ($currentTenant->color_secondary ?? '#c8a84b');
        $tertiary        = $customization->color_tertiary  ?? ($currentTenant->color_tertiary  ?? '#f5f5f5');
        $logoTenantLight = $customization->logo_vertical   ?? null;
        $logoTenantDark  = $customization->logo_negative   ?? $logoTenantLight;
        $authUser        = \Illuminate\Support\Facades\DB::table('users')->where('id', auth()->id())->first();
        $userProfile     = \Illuminate\Support\Facades\DB::table('profile_user')
            ->join('profiles', 'profiles.id', '=', 'profile_user.profile_id')
            ->where('profile_user.user_id', auth()->id())
            ->value('profiles.name');

        // Módulos ativos do tenant
        $tenantModules = \Illuminate\Support\Facades\DB::table('modules')
            ->where('is_active', true)->orderBy('id')->get();

        $hasClientes  = $tenantModules->contains('slug', 'clientes');
        $hasProcessos = Route::has('processos.create');

        // Itens do menu Configurações — aparecem conforme acesso
        $configItems = [];
        if ($hasClientes)  $configItems[] = 'campos_personalizados';
        if ($hasProcessos) $configItems[] = 'pastas';
        if ($hasProcessos) $configItems[] = 'tipos_agendamento';
        if (!empty($currentTenant->publicacoes_enabled)) $configItems[] = 'publicacoes';
    @endphp
    <style>
        :root { --brand-primary:{{ $primary }};--brand-secondary:{{ $secondary }};--brand-tertiary:{{ $tertiary }}; }
        .btn-primary{background-color:var(--brand-primary)!important;border-color:var(--brand-primary)!important;}
        .btn-primary:hover{opacity:.88;}
        .bg-primary{background-color:var(--brand-primary)!important;}
        .text-primary{color:var(--brand-primary)!important;}
        .page-item.active .page-link{background-color:var(--brand-primary)!important;border-color:var(--brand-primary)!important;}
        .page-link{color:var(--brand-primary);}
        .navbar-nav .nav-link.active{background-color:rgba(255,255,255,0.12)!important;}
        .table-responsive{overflow:visible!important;}
        .card{overflow:visible!important;}
        .logo-tenant-light{display:block;}.logo-tenant-dark{display:none;}
        [data-bs-theme="dark"] .logo-tenant-light{display:none;}[data-bs-theme="dark"] .logo-tenant-dark{display:block;}
        [data-sidebar-size="sm"] .collapse.show{display:none!important;}
        [data-sidebar-size="sm"] .nav-sm{display:none!important;}
        #scrollbar{overflow-y:auto;overflow-x:hidden;}
        #scrollbar::-webkit-scrollbar{width:4px;}
        #scrollbar::-webkit-scrollbar-track{background:transparent;}
        #scrollbar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.15);border-radius:4px;}
        #scrollbar::-webkit-scrollbar-thumb:hover{background:rgba(255,255,255,.3);}
        #btn-attendance-topbar{display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;border:2px solid var(--brand-primary);color:var(--brand-primary);background:transparent;font-size:13px;font-weight:600;transition:all .2s;white-space:nowrap;}
        #btn-attendance-topbar:hover{background:var(--brand-primary);color:#fff;}
        #btn-attendance-topbar.btn-attendance-active{background:var(--brand-primary);color:#fff;}
        #btn-attendance-topbar.btn-attendance-active .att-pulse{display:inline-block;}
        .att-pulse{display:none;width:8px;height:8px;background:#0ab39c;border-radius:50%;animation:pulse-dot 1.5s infinite;flex-shrink:0;}
        @keyframes pulse-dot{0%{transform:scale(1);opacity:1;}50%{transform:scale(1.5);opacity:.5;}100%{transform:scale(1);opacity:1;}}
    </style>
</head>
<body>
<div id="layout-wrapper">

{{-- TOPBAR --}}
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ route('dashboard') }}" class="logo">
                        <span class="logo-lg">
                            @if($logoTenantLight)
                                <img src="{{ $logoTenantLight }}" height="32" class="logo-tenant-light" style="object-fit:contain;max-width:140px;">
                                <img src="{{ $logoTenantDark }}"  height="32" class="logo-tenant-dark"  style="object-fit:contain;max-width:140px;">
                            @else
                                <img src="{{ asset('assets/images/ajudy/logo-vertical-negativa.png') }}" height="28" alt="Ajudy">
                            @endif
                        </span>
                        <span class="logo-sm"><img src="{{ asset('assets/images/ajudy/logo-sm.png') }}" height="22"></span>
                    </a>
                </div>
                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon"><span></span><span></span><span></span></span>
                </button>
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center ms-2">
                    @if($logoTenantLight)
                        <img src="{{ $logoTenantLight }}" class="logo-tenant-light" style="height:56px;width:auto;object-fit:contain;max-width:220px;">
                        <img src="{{ $logoTenantDark }}"  class="logo-tenant-dark"  style="height:56px;width:auto;object-fit:contain;max-width:220px;">
                    @else
                        <span class="fw-semibold text-muted fs-14">{{ $currentTenant->name ?? '' }}</span>
                    @endif
                </a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btn-attendance-topbar" title="Atendimento rápido">
                    <span class="att-pulse"></span>
                    <i class="ri-customer-service-2-line fs-16"></i>
                    <span id="btn-attendance-text">Iniciar atendimento</span>
                </button>
                <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="btn-fullscreen">
                    <i class="bx bx-fullscreen fs-22" id="fullscreen-icon"></i>
                </button>
                <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="btn-dark-mode">
                    <i class="bx bx-moon fs-22" id="dark-mode-icon"></i>
                </button>
                <div class="dropdown ms-sm-2 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown">
                        <span class="d-flex align-items-center">
                            @if($authUser->avatar)
                                <img src="{{ asset($authUser->avatar) }}" class="rounded-circle header-profile-user" style="width:36px;height:36px;object-fit:cover;flex-shrink:0;">
                            @else
                                <span class="rounded-circle header-profile-user bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;font-size:15px;flex-shrink:0;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            @endif
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->name }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{ $userProfile ?? 'Usuário' }}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Olá, {{ explode(' ', auth()->user()->name)[0] }}!</h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('perfil.show') }}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> Perfil</a>
                        <form method="POST" action="{{ route('lock.activate') }}">@csrf<button type="submit" class="dropdown-item w-100 text-start"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> Bloquear tela</button></form>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item text-danger w-100 text-start"><i class="mdi mdi-logout fs-16 align-middle me-1"></i> Sair</button></form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- SIDEBAR --}}
<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm"><img src="{{ asset('assets/images/ajudy/logo-sm.png') }}" height="22"></span>
            <span class="logo-lg"><img src="{{ asset('assets/images/ajudy/logo-vertical-negativa.png') }}" height="28"></span>
        </a>
        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm"><img src="{{ asset('assets/images/ajudy/logo-sm.png') }}" height="22"></span>
            <span class="logo-lg"><img src="{{ asset('assets/images/ajudy/logo-vertical-negativa.png') }}" height="28"></span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover"><i class="ri-record-circle-line"></i></button>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <ul class="navbar-nav" id="navbar-nav">

                <li class="menu-title"><span>Menu</span></li>

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ri-dashboard-2-line"></i><span>Dashboard</span>
                    </a>
                </li>

                {{-- Atendimentos --}}
                <li class="nav-item">
                    <a href="{{ route('atendimentos.index') }}" class="nav-link menu-link {{ request()->routeIs('atendimentos.*') ? 'active' : '' }}">
                        <i class="ri-customer-service-2-line"></i><span>Atendimentos</span>
                    </a>
                </li>

                @if($tenantModules->isNotEmpty())
                <li class="menu-title mt-2"><span>Módulos</span></li>

                @foreach($tenantModules as $module)
                <li class="nav-item">

                    @if($module->slug === 'gestao-de-usuarios')
                    {{-- Gestão de Usuários --}}
                    <a href="#sidebarUsuarios"
                       class="nav-link menu-link {{ request()->is('usuarios*','perfis*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('usuarios*','perfis*') ? 'true' : 'false' }}">
                        <i class="{{ $module->icon ?? 'ri-user-settings-line' }}"></i><span>{{ $module->name }}</span>
                    </a>
                    <div class="collapse {{ request()->is('usuarios*','perfis*') ? 'show' : '' }}" id="sidebarUsuarios">
                        <ul class="nav nav-sm flex-column">
                            @if(Route::has('usuarios.index'))
                            <li class="nav-item"><a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"><i class="ri-user-line me-1"></i> Usuários</a></li>
                            <li class="nav-item"><a href="{{ route('perfis.index') }}" class="nav-link {{ request()->routeIs('perfis.*') ? 'active' : '' }}"><i class="ri-shield-user-line me-1"></i> Perfis</a></li>
                            @endif
                        </ul>
                    </div>

                    @elseif($module->slug === 'clientes')
                    {{-- Clientes --}}
                    <a href="#sidebarClientes"
                       class="nav-link menu-link {{ request()->is('clientes*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('clientes*') ? 'true' : 'false' }}">
                        <i class="{{ $module->icon ?? 'ri-user-smile-fill' }}"></i><span>{{ $module->name }}</span>
                    </a>
                    <div class="collapse {{ request()->is('clientes*') ? 'show' : '' }}" id="sidebarClientes">
                        <ul class="nav nav-sm flex-column">
                            @if(Route::has('clientes.create'))
                            <li class="nav-item"><a href="{{ route('clientes.create') }}" class="nav-link {{ request()->routeIs('clientes.create') ? 'active' : '' }}"><i class="ri-user-add-line me-1"></i> Adicionar</a></li>
                            <li class="nav-item"><a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.index','clientes.show') ? 'active' : '' }}"><i class="ri-search-line me-1"></i> Pesquisar</a></li>
                            @endif
                        </ul>
                    </div>

                    @else
                    <a href="{{ isset($module->route_prefix) ? url($module->route_prefix) : '#' }}"
                       class="nav-link menu-link {{ request()->is(($module->route_prefix ?? '__').'*') ? 'active' : '' }}">
                        <i class="{{ $module->icon ?? 'ri-apps-line' }}"></i><span>{{ $module->name }}</span>
                    </a>
                    @endif

                </li>
                @endforeach

                {{-- Processos (dentro do módulo clientes) --}}
                @if($hasProcessos)
                <li class="nav-item">
                    <a href="#sidebarProcessos"
                       class="nav-link menu-link {{ request()->is('processos*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('processos*') ? 'true' : 'false' }}">
                        <i class="ri-scales-line"></i><span>Processos</span>
                    </a>
                    <div class="collapse {{ request()->is('processos*') ? 'show' : '' }}" id="sidebarProcessos">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item"><a href="{{ route('processos.create') }}" class="nav-link {{ request()->routeIs('processos.create') ? 'active' : '' }}"><i class="ri-add-circle-line me-1"></i> Novo processo</a></li>
                            <li class="nav-item"><a href="{{ route('processos.buscar') }}" class="nav-link {{ request()->routeIs('processos.buscar') ? 'active' : '' }}"><i class="ri-search-2-line me-1"></i> Pesquisar</a></li>
                        </ul>
                    </div>
                </li>
                @endif

                {{-- Configurações (dinâmico) --}}
                @if(count($configItems) > 0)
                <li class="menu-title mt-2"><span>Configurações</span></li>
                <li class="nav-item">
                    <a href="#sidebarConfig"
                       class="nav-link menu-link {{ request()->is('clientes/campos*','processos/pastas*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('clientes/campos*','processos/pastas*') ? 'true' : 'false' }}">
                        <i class="ri-settings-3-line"></i><span>Configurações</span>
                    </a>
                    <div class="collapse {{ request()->is('clientes/campos*','processos/pastas*') ? 'show' : '' }}" id="sidebarConfig">
                        <ul class="nav nav-sm flex-column">
                            @if(in_array('campos_personalizados', $configItems) && Route::has('clientes.campos.index'))
                            <li class="nav-item">
                                <a href="{{ route('clientes.campos.index') }}" class="nav-link {{ request()->routeIs('clientes.campos.*') ? 'active' : '' }}">
                                    <i class="ri-list-settings-line me-1"></i> Campos personalizados
                                </a>
                            </li>
                            @endif
                            @if(in_array('pastas', $configItems) && Route::has('processos.pastas.index'))
                            <li class="nav-item">
                                <a href="{{ route('processos.pastas.index') }}" class="nav-link {{ request()->routeIs('processos.pastas.*') ? 'active' : '' }}">
                                    <i class="ri-folder-settings-line me-1"></i> Pastas
                                </a>
                            </li>
                            @endif
                            @if(in_array('tipos_agendamento', $configItems) && Route::has('agendamentos.tipos.index'))
                            <li class="nav-item">
                                <a href="{{ route('agendamentos.tipos.index') }}" class="nav-link {{ request()->routeIs('agendamentos.tipos.*') ? 'active' : '' }}">
                                    <i class="ri-calendar-check-line me-1"></i> Tipos de agendamento
                                </a>
                            </li>
                            @endif
                            @if(in_array('publicacoes', $configItems) && Route::has('publicacoes.config.index'))
                            <li class="nav-item">
                                <a href="{{ route('publicacoes.config.index') }}"
                                   class="nav-link {{ request()->routeIs('publicacoes.config.*') ? 'active' : '' }}">
                                    <i class="ri-newspaper-line me-1"></i> Publicações
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                @endif

            </ul>
        </div>
    </div>
</div>

{{-- CONTEÚDO --}}
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @hasSection('page-title')
            <div class="row"><div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between"
                     style="background-color:var(--brand-primary);margin:-24px -24px 24px;padding:16px 24px;">
                    <h4 style="color:#fff;margin:0;">@yield('page-title')</h4>
                    @hasSection('page-actions')<div>@yield('page-actions')</div>@endif
                </div>
            </div></div>
            @endif
            @yield('content')
        </div>
    </div>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">{{ date('Y') }} &copy; {{ $currentTenant->name ?? 'Ajudy' }}</div>
                <div class="col-sm-6 text-end"><small class="text-muted">Powered by Ajudy</small></div>
            </div>
        </div>
    </footer>
</div>

{{-- Modal exclusão --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0"><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center pb-2">
                <div class="avatar-md mx-auto mb-3"><div class="avatar-title bg-danger-subtle text-danger rounded-circle fs-36"><i class="ri-delete-bin-line"></i></div></div>
                <h5 class="mb-1">Confirmar exclusão</h5>
                <p class="text-muted mb-0" id="deleteModalMessage"></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="deleteModalConfirm">Sim, remover</button>
            </div>
        </div>
    </div>
</div>

</div>{{-- layout-wrapper --}}

<div class="position-fixed top-0 end-0 p-3" style="z-index:9999" id="toast-container"></div>

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

@stack('scripts')

@include('partials.attendance-modal')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const savedMode = localStorage.getItem('ajudy-color-mode') || 'light';
    const iconDark  = document.getElementById('dark-mode-icon');
    if (savedMode === 'dark') { document.documentElement.setAttribute('data-bs-theme','dark'); iconDark?.classList.replace('bx-moon','bx-sun'); }

    const hamburger = document.getElementById('topnav-hamburger-icon');
    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (window.innerWidth >= 1025) {
                const cur = document.documentElement.getAttribute('data-sidebar-size');
                document.documentElement.setAttribute('data-sidebar-size', cur === 'sm' ? 'lg' : 'sm');
            } else { document.body.classList.toggle('vertical-sidebar-enable'); }
        });
    }

    const btnFS = document.getElementById('btn-fullscreen');
    const icoFS = document.getElementById('fullscreen-icon');
    if (btnFS) {
        btnFS.addEventListener('click', function () {
            if (!document.fullscreenElement) { document.documentElement.requestFullscreen(); icoFS.classList.replace('bx-fullscreen','bx-exit-fullscreen'); }
            else { document.exitFullscreen(); icoFS.classList.replace('bx-exit-fullscreen','bx-fullscreen'); }
        });
        document.addEventListener('fullscreenchange', function () { if (!document.fullscreenElement) icoFS.classList.replace('bx-exit-fullscreen','bx-fullscreen'); });
    }

    const btnDark = document.getElementById('btn-dark-mode');
    if (btnDark) {
        btnDark.addEventListener('click', function () {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
            iconDark.classList.replace(isDark ? 'bx-sun':'bx-moon', isDark ? 'bx-moon':'bx-sun');
            localStorage.setItem('ajudy-color-mode', isDark ? 'light' : 'dark');
        });
    }

    let deleteAction = null;
    window.confirmDelete = function (action, message) {
        deleteAction = action;
        document.getElementById('deleteModalMessage').textContent = message || 'Esta ação não pode ser desfeita.';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    };
    document.getElementById('deleteModalConfirm').addEventListener('click', function () {
        if (!deleteAction) return;
        const form = document.createElement('form');
        form.method = 'POST'; form.action = deleteAction;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form); form.submit();
    });
});

// ── Proteção contra botão "Voltar" após lock screen ─────────────────────
// O bfcache (back/forward cache) do browser restaura páginas sem fazer
// nova requisição ao servidor — por isso o middleware não é acionado.
// O evento 'pageshow' com persisted=true é disparado EXATAMENTE nesse caso.
// É a única forma confiável de detectar restauração do bfcache.
window.addEventListener('pageshow', function (e) {
    if (e.persisted) {
        // Página foi restaurada do bfcache (botão Voltar/Avançar)
        // Força reload para o servidor processar a requisição normalmente
        // e o HandleLockScreen redirecionar se necessário
        window.location.reload();
    }
});

function showToast(type, message) {
    const c = {success:{bg:'bg-success',icon:'ri-checkbox-circle-line'},error:{bg:'bg-danger',icon:'ri-close-circle-line'},warning:{bg:'bg-warning',icon:'ri-alert-line'},info:{bg:'bg-info',icon:'ri-information-line'}}[type] || {bg:'bg-info',icon:'ri-information-line'};
    const id = 'toast-' + Date.now();
    document.getElementById('toast-container').insertAdjacentHTML('beforeend',
        `<div id="${id}" class="toast align-items-center text-white ${c.bg} border-0 show mb-2" role="alert"><div class="d-flex"><div class="toast-body d-flex align-items-center gap-2"><i class="${c.icon} fs-16"></i><span>${message}</span></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`
    );
    setTimeout(() => document.getElementById(id)?.remove(), 4000);
}

@if(session('success')) showToast('success', {!! json_encode(session('success')) !!}); @endif
@if(session('error'))   showToast('error',   {!! json_encode(session('error'))   !!}); @endif
@if(session('warning')) showToast('warning', {!! json_encode(session('warning')) !!}); @endif
</script>
</body>
</html>