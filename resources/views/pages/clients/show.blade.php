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
                    <div class="copyable mb-3 pb-3 border-bottom" data-value="{{ $maritalLabels[$client->marital_status] ?? '' }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-user-heart-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Estado civil</span>
                        </div>
                        <div class="text-muted fs-13 ps-4">
                            {{ $maritalLabels[$client->marital_status] ?? '' }}
                        </div>
                    </div>
                    @endif

                    @if($client->nationality)
                    <div class="copyable mb-3 pb-3 border-bottom" data-value="{{ $client->nationality }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-earth-fill text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Nacionalidade</span>
                        </div>
                        <div class="text-muted fs-13 ps-4">
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
                    <div class="copyable mb-3 pb-3 border-bottom" data-value="{{ \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-cake-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Data de nascimento</span>
                        </div>
                        <div class="text-muted fs-13 ps-4">
                            {{ \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') }}
                            @if($age) <span class="text-muted">({{ $age }} anos)</span> @endif
                        </div>
                    </div>
                    @endif

                    @if($client->email)
                    <div class="copyable mb-3 pb-3 border-bottom" data-value="{{ $client->email }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-mail-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">E-mail</span>
                        </div>
                        <div class="text-muted fs-13 ps-4">
                            {{ $client->email }}
                        </div>
                    </div>
                    @endif

                    @if($client->profession)
                    <div class="copyable mb-3 pb-3 border-bottom" data-value="{{ $client->profession }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-briefcase-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Profissão</span>
                        </div>
                        <div class="text-muted fs-13 ps-4">
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
                        @php
                            $fullAddress = collect([
                                $client->address_street,
                                $client->address_neighborhood,
                                $client->address_city,
                                $client->address_state,
                                $client->address_zip ? 'CEP: '.$client->address_zip : null,
                            ])->filter()->implode(', ');
                        @endphp
                    <div class="copyable mb-3 {{ $customFields->whereNotNull('value')->isNotEmpty() ? 'pb-3 border-bottom' : '' }}" data-value="{{ $fullAddress }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-map-pin-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">Endereço</span>
                        </div>
                        <div class="text-muted fs-13 ps-4">
                            {{ $fullAddress }}
                        </div>
                    </div>
                    @endif

                    {{-- Campos personalizados --}}
                    @foreach($customFields->whereNotNull('value') as $field)
                    <div class="copyable mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}" data-value="{{ $field->value }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-list-check text-primary fs-16"></i>
                            <span class="fw-semibold fs-13">{{ $field->label }}</span>
                        </div>
                        <div class="text-muted fs-13 ps-4">
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
<style>
    .copyable {
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 6px;
    }
    /* Estilo para quando a classe está no container principal (bloco inteiro) */
    div.copyable.mb-3 {
        padding: 8px;
        margin: -8px -8px 0 !important;
    }
    /* Estilo para quando a classe está em elementos internos (listas) */
    div.copyable:not(.mb-3) {
        padding: 2px 8px;
        margin: 0 -8px;
    }
    .copyable:hover {
        background-color: rgba(64, 81, 137, 0.1);
    }
    .copyable:active {
        background-color: rgba(64, 81, 137, 0.15);
        transform: scale(0.99);
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyToClipboard = async (text) => {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return true;
            } else {
                throw new Error('Clipboard API unavailable or not secure context');
            }
        } catch (err) {
            console.warn('Usando fallback para cópia:', err.message);
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            textArea.style.top = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                return successful;
            } catch (fallbackErr) {
                console.error('Fallback falhou:', fallbackErr);
                document.body.removeChild(textArea);
                return false;
            }
        }
    };

    document.querySelectorAll('.copyable').forEach(el => {
        el.title = 'Clique para copiar';

        el.addEventListener('click', async function (e) {
            e.stopPropagation();
            const value = this.getAttribute('data-value');
            
            console.log('Tentando copiar:', value);
            
            if (!value) {
                console.warn('Nenhum valor encontrado para copiar');
                return;
            }

            const success = await copyToClipboard(value);
            
            if (success) {
                showToast('info', '<i class="ri-clipboard-line me-1"></i> Copiado: ' + value);
                
                // Feedback visual temporário no elemento
                const originalBg = this.style.backgroundColor;
                this.style.backgroundColor = 'rgba(64, 81, 137, 0.2)';
                setTimeout(() => {
                    this.style.backgroundColor = originalBg;
                }, 200);
            } else {
                showToast('error', 'Falha ao copiar para a área de transferência');
            }
        });
    });
});
</script>
@endpush

@endsection
