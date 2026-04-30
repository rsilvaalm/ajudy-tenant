@extends('layouts.app')
@section('title', $client->name)
@section('page-title', $client->name)

@section('page-actions')
<a href="{{ route('clientes.edit', $client->id) }}" class="btn btn-sm btn-light">
    <i class="ri-pencil-line me-1"></i> Editar
</a>
@endsection

@section('content')

@php
    $maritalLabels = [
        'solteiro'      => 'Solteiro(a)',
        'casado'        => 'Casado(a)',
        'divorciado'    => 'Divorciado(a)',
        'viuvo'         => 'Viúvo(a)',
        'uniao_estavel' => 'União estável',
        'outro'         => 'Outro',
    ];
@endphp

<div class="row g-4">

    {{-- ── Sidebar com informações ──────────────────────────────────────── --}}
    <div class="col-lg-3">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-body p-0">
                {{-- Informações clicáveis --}}
                <div class="p-3">

                    @if($client->marital_status)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-user-heart-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Estado civil</span>
                        </div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->marital_status }}">
                            {{ $maritalLabels[$client->marital_status] ?? '' }}
                        </div>
                    </div>
                    @endif

                    @if($client->nationality)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-earth-fill text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Nacionalidade</span>
                        </div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->nationality }}">
                            {{ $client->nationality }}
                        </div>
                    </div>
                    @endif

                    @if($client->father_name || $client->mother_name)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-group-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Filiação</span>
                        </div>
                        @if($client->father_name)
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->father_name }}">
                            {{ $client->father_name }}
                        </div>
                        @endif
                        @if($client->mother_name)
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->mother_name }}">
                            {{ $client->mother_name }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($client->birth_date)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-cake-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Data de nascimento</span>
                        </div>
                        <div class="copyable text-muted fs-13 ps-4"
                             data-value="{{ \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') }}">
                            {{ \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') }}
                        </div>                        
                        <div class="copyable text-muted fs-13 ps-4"
                             data-value="{{ \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') }}">
                            @if($age) <span class="text-muted">{{ $age }} anos</span> @endif
                        </div>
                    </div>
                    @endif

                    @if($client->email)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-mail-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">E-mail</span>
                        </div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->email }}">
                            {{ $client->email }}
                        </div>
                    </div>
                    @endif

                    @if($client->profession)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-briefcase-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Profissão</span>
                        </div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->profession }}">
                            {{ $client->profession }}
                        </div>
                    </div>
                    @endif

                    @if($client->phone || $client->mobile)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-phone-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Telefone</span>
                        </div>
                        @if($client->phone)
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->phone }}">
                            {{ $client->phone }}
                        </div>
                        @endif
                        @if($client->mobile)
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->mobile }}">
                            {{ $client->mobile }}
                            <span class="badge bg-info-subtle text-info fs-10">Celular</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($client->cpf || $client->rg)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-id-card-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Documentos</span>
                        </div>
                        @if($client->cpf)
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->cpf }}">
                            CPF: {{ $client->cpf }}
                        </div>
                        @endif
                        @if($client->rg)
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->rg }}">
                            RG: {{ $client->rg }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($client->address_street)
                    <div class="mb-3 {{ $customFields->whereNotNull('value')->isNotEmpty() ? 'pb-3 border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-map-pin-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Endereço</span>
                        </div>
                        @php
                            $fullAddress = collect([
                                $client->address_street,
                                $client->address_neighborhood,
                                $client->address_city,
                                $client->address_state,
                                $client->address_zip ? 'CEP: '.$client->address_zip : null,
                            ])->filter()->implode(', ');
                        @endphp
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $fullAddress }}">
                            {{ $fullAddress }}
                        </div>
                    </div>
                    @endif

                    {{-- Campos personalizados --}}
                    @foreach($customFields->whereNotNull('value') as $field)
                    <div class="mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-list-check text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">{{ $field->label }}</span>
                        </div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $field->value }}">
                            {{ $field->value }}
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- ── Conteúdo principal ───────────────────────────────────────────── --}}
    <div class="col-lg-9">

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Processos</h5>
                <button class="btn btn-sm btn-primary" disabled>
                    <i class="ri-add-line me-1"></i> Novo processo
                </button>
            </div>
            <div class="card-body text-center text-muted py-4">
                <i class="ri-scales-line fs-36 d-block mb-2 opacity-25"></i>
                <small>Módulo de processos em desenvolvimento.</small>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Atendimentos e anotações</h5>
                <button class="btn btn-sm btn-primary" disabled>
                    <i class="ri-add-line me-1"></i> Novo registro
                </button>
            </div>
            <div class="card-body text-center text-muted py-4">
                <i class="ri-customer-service-2-line fs-36 d-block mb-2 opacity-25"></i>
                <small>Módulo de atendimentos em desenvolvimento.</small>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
document.querySelectorAll('.copyable').forEach(el => {
    el.style.cursor = 'pointer';
    el.title = 'Clique para copiar';

    el.addEventListener('mouseenter', function () {
        this.style.opacity = '.7';
    });
    el.addEventListener('mouseleave', function () {
        this.style.opacity = '1';
    });

    el.addEventListener('click', function () {
        const value = this.getAttribute('data-value');
        if (!value) return;

        navigator.clipboard.writeText(value).then(() => {
            showToast('info', '<i class="ri-clipboard-line me-1"></i> Copiado: ' + value);
        }).catch(() => {
            // Fallback para navegadores sem clipboard API
            const ta = document.createElement('textarea');
            ta.value = value;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast('info', 'Copiado: ' + value);
        });
    });
});
</script>
@endpush

@endsection
