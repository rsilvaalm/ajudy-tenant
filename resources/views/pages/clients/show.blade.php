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

    {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
    <div class="col-lg-3">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-body p-0">
                <div class="p-3 border-bottom text-center"
                     style="background:var(--brand-primary);border-radius:calc(.375rem - 1px) calc(.375rem - 1px) 0 0;">
                    <h6 class="text-white mb-1 fw-bold">{{ $client->name }}</h6>
                    @if($client->marital_status)
                        <small class="text-white opacity-75 d-block">{{ $maritalLabels[$client->marital_status] ?? '' }}</small>
                    @endif
                    @if($client->nationality)
                        <small class="text-white opacity-75 d-block">{{ $client->nationality }}</small>
                    @endif
                </div>
                <div class="p-3">
                    @if($client->father_name || $client->mother_name)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-group-line text-primary fs-16"></i><span class="fw-semibold fs-13">Filiação</span></div>
                        @if($client->father_name)<div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->father_name }}">{{ $client->father_name }}</div>@endif
                        @if($client->mother_name)<div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->mother_name }}">{{ $client->mother_name }}</div>@endif
                    </div>
                    @endif
                    @if($client->birth_date)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-cake-line text-primary fs-16"></i><span class="fw-semibold fs-13">Data de nascimento</span></div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') }}">
                            {{ \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') }}
                            @if($age) <span>({{ $age }} anos)</span> @endif
                        </div>
                    </div>
                    @endif
                    @if($client->email)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-mail-line text-primary fs-16"></i><span class="fw-semibold fs-13">E-mail</span></div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->email }}">{{ $client->email }}</div>
                    </div>
                    @endif
                    @if($client->profession)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-briefcase-line text-primary fs-16"></i><span class="fw-semibold fs-13">Profissão</span></div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->profession }}">{{ $client->profession }}</div>
                    </div>
                    @endif
                    @if($client->phone || $client->mobile)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-phone-line text-primary fs-16"></i><span class="fw-semibold fs-13">Telefone</span></div>
                        @if($client->phone)<div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->phone }}">{{ $client->phone }}</div>@endif
                        @if($client->mobile)<div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->mobile }}">{{ $client->mobile }} <span class="badge bg-info-subtle text-info fs-10">Celular</span></div>@endif
                    </div>
                    @endif
                    @if($client->cpf || $client->rg)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-id-card-line text-primary fs-16"></i><span class="fw-semibold fs-13">Documentos</span></div>
                        @if($client->cpf)<div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->cpf }}">CPF: {{ $client->cpf }}</div>@endif
                        @if($client->rg)<div class="copyable text-muted fs-13 ps-4" data-value="{{ $client->rg }}">RG: {{ $client->rg }}</div>@endif
                    </div>
                    @endif
                    @if($client->address_street)
                    <div class="mb-3 {{ $customFields->whereNotNull('value')->isNotEmpty() ? 'pb-3 border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-map-pin-line text-primary fs-16"></i><span class="fw-semibold fs-13">Endereço</span></div>
                        @php
                            $fullAddress = collect([$client->address_street,$client->address_neighborhood,$client->address_city,$client->address_state,$client->address_zip?'CEP:'.$client->address_zip:null])->filter()->implode(', ');
                        @endphp
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $fullAddress }}">{{ $fullAddress }}</div>
                    </div>
                    @endif
                    @foreach($customFields->whereNotNull('value') as $field)
                    <div class="mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-1"><i class="ri-list-check text-primary fs-16"></i><span class="fw-semibold fs-13">{{ $field->label }}</span></div>
                        <div class="copyable text-muted fs-13 ps-4" data-value="{{ $field->value }}">{{ $field->value }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Conteúdo ──────────────────────────────────────────────────────── --}}
    <div class="col-lg-9">

        {{-- Processos --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Processos</h5>
                <button class="btn btn-sm btn-primary"
                        id="btn-new-process"
                        data-bs-toggle="modal"
                        data-bs-target="#newProcessModal">
                    <i class="ri-add-line me-1"></i> Novo processo
                </button>
            </div>
            <div class="card-body p-0">
                <div id="client-processes-list">
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Atendimentos (desativado temporariamente) --}}
        {{--
        <div class="card mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Atendimentos</h5></div>
            ...
        </div>
        --}}

    </div>
</div>

{{-- Inclui modal de novo processo com dados do cliente --}}
@include('partials.new-process-modal')

@push('scripts')
<script>
    const _clientId   = {{ $client->id }};
    const _clientName = '{{ addslashes($client->name) }}';
    const authUserId  = {{ auth()->id() }};

    // Passa dados do cliente para o modal
    document.getElementById('newProcessModal').dataset.clientId   = _clientId;
    document.getElementById('newProcessModal').dataset.clientName = _clientName;

    // ── Carrega processos do cliente ──────────────────────────────────────
    function loadClientProcesses() {
        const list = document.getElementById('client-processes-list');
        list.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';

        fetch(`/processos/cliente/${_clientId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(processes => {
            if (!processes.length) {
                list.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="ri-scales-line fs-36 d-block mb-2 opacity-25"></i>
                        <small>Nenhum processo cadastrado.</small>
                    </div>`;
                return;
            }

            list.innerHTML = '';
            const table = document.createElement('div');
            table.className = 'table-responsive';
            table.innerHTML = `
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Pasta</th>
                            <th>Data</th>
                            <th>Polo ativo</th>
                            <th>Polo passivo</th>
                            <th class="text-end" style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody id="processes-tbody"></tbody>
                </table>
            `;
            list.appendChild(table);

            const tbody = document.getElementById('processes-tbody');
            processes.forEach(p => {
                const date       = formatDate(p.date);
                const folderBadge = p.folder_name
                    ? `<span class="badge fs-11" style="background:${p.folder_color || '#1a3c5e'};color:#fff;">${p.folder_name}</span>`
                    : '<span class="text-muted">—</span>';

                const tr = document.createElement('tr');
                tr.style.cursor = 'pointer';
                tr.innerHTML = `
                    <td class="fw-medium fs-13" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${p.number}">${p.number}</td>
                    <td>${folderBadge}</td>
                    <td class="text-muted">${date}</td>
                    <td class="text-muted fs-13" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${p.active_pole}</td>
                    <td class="text-muted fs-13" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${p.passive_pole}</td>
                    <td class="text-end">
                        <a href="/processos/${p.id}" class="btn btn-sm btn-soft-primary" title="Ver processo">
                            <i class="ri-eye-line"></i>
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(() => {
            list.innerHTML = '<div class="text-center text-muted py-3"><small>Erro ao carregar processos.</small></div>';
        });
    }

    loadClientProcesses();

    // ── Copiar ao clicar ──────────────────────────────────────────────────
    document.querySelectorAll('.copyable').forEach(el => {
        el.style.cursor = 'pointer';
        el.title = 'Clique para copiar';
        el.addEventListener('mouseenter', function () { this.style.opacity = '.7'; });
        el.addEventListener('mouseleave', function () { this.style.opacity = '1'; });
        el.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            if (!value) return;
            navigator.clipboard.writeText(value).then(() => {
                showToast('info', 'Copiado: ' + value);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = value; ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta); ta.select();
                document.execCommand('copy'); document.body.removeChild(ta);
                showToast('info', 'Copiado: ' + value);
            });
        });
    });

    function formatDate(d) {
        if (!d) return '—';
        const [y, m, day] = d.split('-');
        return `${day}/${m}/${y}`;
    }
</script>
@endpush

@endsection
