@extends('layouts.app')
@section('title', 'Processo ' . $process->number)
@section('page-title', 'Processo')

@section('page-actions')
<a href="{{ route('processos.edit', $process->id) }}" class="btn btn-sm btn-light">
    <i class="ri-pencil-line me-1"></i> Editar
</a>
@endsection

@section('content')

{{-- Cabeçalho --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <p class="text-muted fs-11 mb-0 text-uppercase">Número do processo</p>
                <h5 class="fw-bold mb-0">{{ $process->number }}</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if($process->folder_name)
                <span class="badge fs-12" style="background:{{ $process->folder_color ?? '#1a3c5e' }};color:#fff;padding:6px 12px;">
                    <i class="ri-folder-line me-1"></i>{{ $process->folder_name }}
                </span>
                @endif
                <span class="text-muted fs-13">{{ \Carbon\Carbon::parse($process->date)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Abas --}}
<div class="card">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist" id="process-tabs">
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3" data-bs-toggle="tab" href="#tab-info" role="tab"
                   data-anchor="informacoes">
                    <i class="ri-information-line me-1"></i>
                    <span class="d-none d-sm-inline">Informações</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3" data-bs-toggle="tab" href="#tab-agenda" role="tab"
                   id="tab-agenda-btn" data-anchor="agendamentos">
                    <i class="ri-calendar-line me-1"></i>
                    <span class="d-none d-sm-inline">Agendamentos</span>
                    <span id="badge-agendamentos" class="badge bg-warning-subtle text-warning ms-1" style="display:none;"></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3" data-bs-toggle="tab" href="#tab-anotacoes" role="tab"
                   id="tab-anotacoes-btn" data-anchor="anotacoes">
                    <i class="ri-sticky-note-line me-1"></i>
                    <span class="d-none d-sm-inline">Anotações</span>
                    <span id="badge-anotacoes" class="badge bg-primary-subtle text-primary ms-1" style="display:none;"></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-andamento" role="tab"
                   id="tab-andamento-btn" data-anchor="movimentacoes">
                    <i class="ri-time-line me-1"></i>
                    <span class="d-none d-sm-inline">Movimentações</span>
                    <span id="badge-movimentacoes" class="badge bg-secondary-subtle text-secondary ms-1" style="display:none;"></span>
                </a>
            </li>
            @if(!empty($currentTenant->publicacoes_enabled))
            <li class="nav-item" role="presentation" id="tab-publicacoes-nav">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-publicacoes" role="tab"
                   id="tab-publicacoes-btn" data-anchor="publicacoes">
                    <i class="ri-newspaper-line me-1"></i>
                    <span class="d-none d-sm-inline">Publicações</span>
                    <span id="badge-publicacoes" class="badge bg-info-subtle text-info ms-1" style="display:none;"></span>
                </a>
            </li>
            @endif
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-despesas" role="tab"
                   data-anchor="despesas">
                    <i class="ri-money-dollar-circle-line me-1"></i>
                    <span class="d-none d-sm-inline">Despesas</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-honorarios" role="tab"
                   data-anchor="honorarios">
                    <i class="ri-hand-coin-line me-1"></i>
                    <span class="d-none d-sm-inline">Honorários</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body tab-content p-4">

        {{-- Informações --}}
        <div class="tab-pane" id="tab-info" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <h6 class="fw-semibold text-muted text-uppercase fs-11 mb-3">Dados do processo</h6>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Polo ativo</label>
                            <p class="fw-medium mb-0">{{ $process->active_pole }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Polo passivo</label>
                            <p class="fw-medium mb-0">{{ $process->passive_pole }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Local</label>
                            <p class="fw-medium mb-0 text-muted">{{ $process->location ?: '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted fs-12 mb-1">Data</label>
                            <p class="fw-medium mb-0">{{ \Carbon\Carbon::parse($process->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-semibold text-muted text-uppercase fs-11 mb-3">Cliente</h6>
                    <div class="p-3 rounded border d-flex align-items-center gap-3">
                        <div class="avatar-sm flex-shrink-0">
                            <div class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold fs-16">
                                {{ strtoupper(substr($process->client_name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <p class="fw-semibold mb-0 text-truncate">{{ $process->client_name }}</p>
                        </div>
                        <a href="{{ route('clientes.show', $process->client_id) }}"
                           class="btn btn-sm btn-soft-primary flex-shrink-0">
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Agendamentos --}}
        <div class="tab-pane" id="tab-agenda" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="fw-semibold mb-0">Agendamentos</h6>
                <button class="btn btn-sm btn-primary" id="btn-new-schedule">
                    <i class="ri-add-line me-1"></i> Novo agendamento
                </button>
            </div>
            <div id="schedules-list">
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>
        </div>

        {{-- Anotações --}}
        <div class="tab-pane" id="tab-anotacoes" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="fw-semibold mb-0">Anotações</h6>
                <button class="btn btn-sm btn-primary" id="btn-new-note">
                    <i class="ri-add-line me-1"></i> Nova anotação
                </button>
            </div>
            <div id="notes-list">
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="tab-andamento" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h6 class="fw-semibold mb-0">Movimentações</h6>
                    <small class="text-muted" id="mov-process-data"></small>
                    <div id="mov-last-synced" class="fs-12 text-muted">Nunca sincronizado</div>
                </div>
                <button class="btn btn-sm btn-primary" id="btn-sync-datajud">
                    <i class="ri-refresh-line me-1"></i> Sincronizar DataJud
                </button>
            </div>
            <div id="movements-list">
                <div class="text-center text-muted py-5">
                    <i class="ri-time-line fs-36 d-block mb-2 opacity-25"></i>
                    <p class="mb-1">Nenhuma movimentação carregada.</p>
                    <small>Clique em <strong>Sincronizar DataJud</strong> para buscar as movimentações oficiais.</small>
                </div>
            </div>
        </div>

        @if(!empty($currentTenant->publicacoes_enabled))
        <div class="tab-pane" id="tab-publicacoes" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h6 class="fw-semibold mb-0">Publicações</h6>
                    <small class="text-muted" id="pub-usage-label"></small>
                </div>
                <button class="btn btn-sm btn-primary" id="btn-sync-publicacoes">
                    <i class="ri-refresh-line me-1"></i> Buscar publicações
                </button>
            </div>

            {{-- Consumo mensal --}}
            <div id="pub-usage-bar" class="mb-4" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-muted fs-12">Consumo este mês</small>
                    <small class="text-muted fs-12" id="pub-usage-text"></small>
                </div>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar" id="pub-usage-progress" role="progressbar" style="width:0%"></div>
                </div>
            </div>

            <div id="publications-list">
                <div class="text-center text-muted py-5">
                    <i class="ri-newspaper-line fs-36 d-block mb-2 opacity-25"></i>
                    <p class="mb-1">Nenhuma publicação carregada.</p>
                    <small>Clique em <strong>Buscar publicações</strong> para consultar o Escavador.</small>
                </div>
            </div>
        </div>
        @endif

        <div class="tab-pane" id="tab-despesas" role="tabpanel">
            <div class="text-center text-muted py-5">
                <i class="ri-money-dollar-circle-line fs-48 d-block mb-2 opacity-25"></i>
                <p class="mb-0">Em desenvolvimento.</p>
            </div>
        </div>
        <div class="tab-pane" id="tab-honorarios" role="tabpanel">
            <div class="text-center text-muted py-5">
                <i class="ri-hand-coin-line fs-48 d-block mb-2 opacity-25"></i>
                <p class="mb-0">Em desenvolvimento.</p>
            </div>
        </div>

    </div>
</div>

{{-- Modal: Novo Agendamento --}}
<div class="modal fade" id="newScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header align-items-center" style="background:var(--brand-primary);">
                <h5 class="modal-title text-white d-flex align-items-center gap-2 mb-0">
                    <i class="ri-calendar-add-line fs-18"></i> Novo Agendamento
                </h5>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="sched_error" class="alert alert-danger py-2 mb-3 d-none fs-13"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="sched_title" class="form-control sched-upper" placeholder="TÍTULO DO AGENDAMENTO">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo</label>
                        <select id="sched_type_id" class="form-select"><option value="">Selecione...</option></select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="text" id="sched_date" class="form-control" placeholder="DD/MM/AAAA" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hora <span class="text-danger">*</span></label>
                        <input type="text" id="sched_time" class="form-control" placeholder="HH:MM" autocomplete="off">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Destinatários <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" id="sched_recipient_search" class="form-control"
                                   placeholder="Buscar usuário..." autocomplete="off">
                            <div id="sched_recipient_results"
                                 class="position-absolute w-100 bg-white border rounded shadow-lg"
                                 style="top:100%;z-index:999;display:none;max-height:180px;overflow-y:auto;"></div>
                        </div>
                        <div id="sched_recipients_selected" class="d-flex flex-wrap gap-1 mt-2"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea id="sched_description" class="form-control" rows="3"
                                  placeholder="Detalhes do agendamento..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_save_schedule">
                    <i class="ri-save-line me-1"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Nova / Editar Anotação --}}
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header align-items-center" style="background:var(--brand-primary);">
                <h5 class="modal-title text-white d-flex align-items-center gap-2 mb-0" id="note_modal_title">
                    <i class="ri-sticky-note-line fs-18"></i> Nova Anotação
                </h5>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="note_error" class="alert alert-danger py-2 mb-3 d-none fs-13"></div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="text" id="note_date" class="form-control" placeholder="DD/MM/AAAA" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hora <span class="text-danger">*</span></label>
                        <input type="text" id="note_time" class="form-control" placeholder="HH:MM" autocomplete="off">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Anotação <span class="text-danger">*</span></label>
                        <textarea id="note_content" class="form-control" rows="5"
                                  placeholder="Registre aqui a anotação do processo..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_save_note">
                    <i class="ri-save-line me-1"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmação de ação --}}
<div class="modal fade" id="scheduleActionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pb-2">
                <div class="avatar-md mx-auto mb-3">
                    <div id="sched_action_icon_wrap" class="avatar-title rounded-circle fs-36"></div>
                </div>
                <h5 class="mb-1" id="sched_action_title"></h5>
                <p class="text-muted mb-0" id="sched_action_message"></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn" id="sched_action_confirm"></button>
            </div>
        </div>
    </div>
</div>

<style>
/* Agendamentos */
.schedule-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: box-shadow .15s;
    display: flex;
}
.schedule-item:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); }
.sched-side-bar {
    width: 28px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.sched-side-bar span {
    writing-mode: vertical-rl; text-orientation: mixed;
    transform: rotate(180deg);
    font-size: 9px; font-weight: 800; letter-spacing: .12em;
    text-transform: uppercase; color: #fff;
}
.schedule-item.status-open .sched-side-bar  { background: #f7b731; }
.schedule-item.status-done .sched-side-bar  { background: #0ab39c; }
.sched-body { flex: 1; padding: 14px 16px; min-width: 0; }
.sched-meta {
    display: inline-flex; align-items: center; gap: 12px; flex-wrap: wrap;
    background: #f3f4f6; border-radius: 6px; padding: 4px 12px;
    font-size: 12px; color: #6c757d; margin-bottom: 10px;
}
.dest-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--brand-primary); color: #fff;
    font-size: 11px; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0; overflow: hidden;
}
.dest-avatar img { width: 100%; height: 100%; object-fit: cover; }

/* Anotações */
.note-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 14px 16px;
    transition: box-shadow .15s;
}
.note-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.08); }
.note-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: var(--brand-primary); color: #fff;
    font-size: 15px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; overflow: hidden;
}
.note-avatar img { width: 100%; height: 100%; object-fit: cover; }
.note-date-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--brand-primary); color: #fff;
    border-radius: 6px; padding: 4px 10px;
    font-size: 11px; font-weight: 700; letter-spacing: .04em;
}
</style>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/pt.js"></script>
<script>
const PROCESS_ID = {{ $process->id }};
const AUTH_ID    = {{ auth()->id() }};

let schedules    = [];
let selectedRecipients     = [];
let recipientSearchTimeout = null;
let pendingAction          = null;

let notesFpDate   = null;
let notesFpTime   = null;
let editingNoteId = null;

// ════════════════════════════════════════════════════════════════════════════
// ANCORAGEM VIA HASH
// ════════════════════════════════════════════════════════════════════════════
const anchorMap = {
    'informacoes' : '#tab-info',
    'agendamentos': '#tab-agenda',
    'anotacoes'   : '#tab-anotacoes',
    'despesas'    : '#tab-despesas',
    'honorarios'  : '#tab-honorarios',
};

function activateTabByAnchor(anchor) {
    const selector = anchorMap[anchor];
    if (!selector) return false;
    const tabEl = document.querySelector('[href="' + selector + '"]');
    if (tabEl) { bootstrap.Tab.getOrCreateInstance(tabEl).show(); return true; }
    return false;
}

document.addEventListener('DOMContentLoaded', function () {
    // Inicializa flatpickrs de agendamento
    flatpickr('#sched_date', {
        locale: 'pt', dateFormat: 'Y-m-d',
        altInput: true, altFormat: 'd/m/Y', allowInput: true,
    });
    const fpSchedTime = flatpickr('#sched_time', {
        enableTime: true, noCalendar: true,
        dateFormat: 'H:i', time_24hr: true, allowInput: true,
    });
    document.getElementById('sched_time').addEventListener('click', () => fpSchedTime.open());

    // Atualiza hash ao trocar aba
    document.querySelectorAll('[data-anchor]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function () {
            history.replaceState(null, '', '#' + this.dataset.anchor);
        });
    });

    // Ativa aba pelo hash ou padrão = informacoes
    const hash = window.location.hash.replace('#', '');
    if (!activateTabByAnchor(hash)) {
        activateTabByAnchor('informacoes');
    }

    // Carrega badges imediatamente
    loadScheduleBadge();
    loadNoteBadge();
});

// ── Carrega badge de agendamentos ─────────────────────────────────────────
function loadScheduleBadge() {
    fetch('/agendamentos/processo/' + PROCESS_ID, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
        schedules = data;
        const open  = data.filter(s => s.status === 'open').length;
        const total = data.length;
        const badge = document.getElementById('badge-agendamentos');
        if (total > 0) {
            badge.textContent = open + ' de ' + total;
            badge.style.display = 'inline';
        }
    }).catch(() => {});
}

// ── Carrega badge de anotações ────────────────────────────────────────────
function loadNoteBadge() {
    fetch('/processos/' + PROCESS_ID + '/anotacoes', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
        const badge = document.getElementById('badge-anotacoes');
        if (data.length > 0) {
            badge.textContent = data.length;
            badge.style.display = 'inline';
        }
    }).catch(() => {});
}

// ════════════════════════════════════════════════════════════════════════════
// AGENDAMENTOS
// ════════════════════════════════════════════════════════════════════════════
document.getElementById('tab-agenda-btn').addEventListener('shown.bs.tab', () => {
    if (!schedules.length) loadSchedules();
    else renderSchedules();
});

document.querySelectorAll('.sched-upper').forEach(el => {
    el.addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
});

function loadScheduleTypes() {
    fetch('/agendamentos/tipos-json', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(types => {
        const sel = document.getElementById('sched_type_id');
        types.forEach(t => {
            if ([...sel.options].some(o => o.value == t.id)) return;
            const opt = document.createElement('option');
            opt.value = t.id; opt.textContent = t.name;
            sel.appendChild(opt);
        });
    }).catch(() => {});
}

document.getElementById('newScheduleModal').addEventListener('show.bs.modal', function () {
    if (document.getElementById('sched_type_id').options.length <= 1) loadScheduleTypes();
});

// Destinatários
document.getElementById('sched_recipient_search').addEventListener('input', function () {
    clearTimeout(recipientSearchTimeout);
    const q = this.value.trim();
    if (!q) { document.getElementById('sched_recipient_results').style.display = 'none'; return; }
    recipientSearchTimeout = setTimeout(() => searchRecipients(q), 300);
});

function searchRecipients(q) {
    fetch('/agendamentos/buscar-usuarios?q=' + encodeURIComponent(q), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(users => {
        const c = document.getElementById('sched_recipient_results');
        const filtered = users.filter(u => !selectedRecipients.some(r => r.id === u.id));
        c.innerHTML = !filtered.length
            ? '<div class="p-2 text-muted text-center fs-13">Nenhum usuário encontrado.</div>'
            : filtered.map(u => {
                const av = u.avatar
                    ? '<img src="/' + u.avatar + '" style="width:100%;height:100%;object-fit:cover;">'
                    : u.name.charAt(0).toUpperCase();
                return '<div class="p-2 px-3 border-bottom d-flex align-items-center gap-2" style="cursor:pointer;" onclick="addRecipient(' + u.id + ', \'' + u.name.replace(/'/g, "\\'") + '\', \'' + (u.avatar || '') + '\')">'
                    + '<span class="dest-avatar">' + av + '</span>'
                    + '<span class="fs-13">' + u.name + '</span>'
                    + '</div>';
            }).join('');
        c.style.display = 'block';
    });
}

function addRecipient(id, name, avatar) {
    if (selectedRecipients.some(r => r.id === id)) return;
    selectedRecipients.push({ id, name, avatar });
    renderRecipientTags();
    document.getElementById('sched_recipient_search').value = '';
    document.getElementById('sched_recipient_results').style.display = 'none';
}

function removeRecipient(id) {
    selectedRecipients = selectedRecipients.filter(r => r.id !== id);
    renderRecipientTags();
}

function renderRecipientTags() {
    document.getElementById('sched_recipients_selected').innerHTML =
        selectedRecipients.map(r => {
            const av = r.avatar
                ? '<img src="/' + r.avatar + '" style="width:100%;height:100%;object-fit:cover;">'
                : r.name.charAt(0).toUpperCase();
            return '<span class="badge bg-primary-subtle text-primary d-inline-flex align-items-center gap-1 px-2 py-1">'
                + '<span class="dest-avatar" style="width:20px;height:20px;font-size:9px;">' + av + '</span>'
                + r.name
                + '<button type="button" onclick="removeRecipient(' + r.id + ')" class="btn btn-link p-0 text-primary" style="line-height:1;"><i class="ri-close-line fs-12"></i></button>'
                + '</span>';
        }).join('');
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('#sched_recipient_search') && !e.target.closest('#sched_recipient_results'))
        document.getElementById('sched_recipient_results').style.display = 'none';
});

document.getElementById('btn-new-schedule').addEventListener('click', function () {
    ['sched_title','sched_description'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('sched_type_id').value = '';
    document.getElementById('sched_error').classList.add('d-none');
    selectedRecipients = [];
    renderRecipientTags();
    const fpD = document.getElementById('sched_date')._flatpickr;
    const fpT = document.getElementById('sched_time')._flatpickr;
    if (fpD) fpD.clear();
    if (fpT) fpT.clear();
    new bootstrap.Modal(document.getElementById('newScheduleModal')).show();
});

document.getElementById('btn_save_schedule').addEventListener('click', function () {
    const title  = document.getElementById('sched_title').value.trim();
    const typeId = document.getElementById('sched_type_id').value;
    const desc   = document.getElementById('sched_description').value;
    const errEl  = document.getElementById('sched_error');
    const fpD    = document.getElementById('sched_date')._flatpickr;
    const fpT    = document.getElementById('sched_time')._flatpickr;
    const date   = fpD && fpD.selectedDates[0] ? fpD.formatDate(fpD.selectedDates[0], 'Y-m-d') : '';
    const time   = fpT && fpT.selectedDates[0] ? fpT.formatDate(fpT.selectedDates[0], 'H:i') : document.getElementById('sched_time').value;

    errEl.classList.add('d-none');
    if (!title || !date || !time || !selectedRecipients.length) {
        errEl.textContent = 'Preencha título, data, hora e selecione ao menos um destinatário.';
        errEl.classList.remove('d-none');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

    fetch('/agendamentos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            process_id: PROCESS_ID, schedule_type_id: typeId || null,
            title, date, time, description: desc,
            recipient_ids: selectedRecipients.map(r => r.id),
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('newScheduleModal')).hide();
            showToast('success', 'Agendamento criado!');
            loadSchedules();
        } else if (data.errors) {
            errEl.textContent = Object.values(data.errors)[0][0];
            errEl.classList.remove('d-none');
        }
    })
    .catch(() => { errEl.textContent = 'Erro ao salvar.'; errEl.classList.remove('d-none'); })
    .finally(() => { this.disabled = false; this.innerHTML = '<i class="ri-save-line me-1"></i> Salvar'; });
});

// Modal de confirmação
function showActionModal(opts) {
    document.getElementById('sched_action_title').textContent   = opts.title;
    document.getElementById('sched_action_message').textContent = opts.message;
    const wrap = document.getElementById('sched_action_icon_wrap');
    wrap.className = 'avatar-title rounded-circle fs-36 ' + opts.iconBg;
    wrap.innerHTML = opts.iconEl;
    const btn = document.getElementById('sched_action_confirm');
    btn.className   = 'btn ' + opts.btnClass;
    btn.textContent = opts.btnLabel;
    pendingAction   = opts.onConfirm;
    new bootstrap.Modal(document.getElementById('scheduleActionModal')).show();
}

document.getElementById('sched_action_confirm').addEventListener('click', function () {
    if (typeof pendingAction === 'function') {
        bootstrap.Modal.getInstance(document.getElementById('scheduleActionModal')).hide();
        pendingAction();
        pendingAction = null;
    }
});

function completeSchedule(id) {
    showActionModal({
        title: 'Concluir agendamento', message: 'Marcar este agendamento como concluído?',
        iconBg: 'bg-success-subtle text-success', iconEl: '<i class="ri-check-double-line"></i>',
        btnClass: 'btn-success', btnLabel: 'Sim, concluir',
        onConfirm: () => patchSchedule('/agendamentos/' + id + '/concluir', 'Agendamento concluído!'),
    });
}

function reopenSchedule(id) {
    showActionModal({
        title: 'Reabrir agendamento', message: 'Deseja reabrir este agendamento?',
        iconBg: 'bg-warning-subtle text-warning', iconEl: '<i class="ri-restart-line"></i>',
        btnClass: 'btn-warning', btnLabel: 'Sim, reabrir',
        onConfirm: () => patchSchedule('/agendamentos/' + id + '/reabrir', 'Agendamento reaberto!'),
    });
}

function deleteSchedule(id) {
    showActionModal({
        title: 'Remover agendamento', message: 'Esta ação não pode ser desfeita.',
        iconBg: 'bg-danger-subtle text-danger', iconEl: '<i class="ri-delete-bin-line"></i>',
        btnClass: 'btn-danger', btnLabel: 'Sim, remover',
        onConfirm: () => {
            fetch('/agendamentos/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) { showToast('success', 'Agendamento removido!'); loadSchedules(); }
                else showToast('error', data.message || 'Sem permissão.');
            });
        },
    });
}

function patchSchedule(url, msg) {
    fetch(url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { showToast('success', msg); loadSchedules(); }
        else showToast('error', data.message || 'Sem permissão.');
    });
}

function loadSchedules() {
    const list = document.getElementById('schedules-list');
    list.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
    fetch('/agendamentos/processo/' + PROCESS_ID, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
        schedules = data;
        renderSchedules();
        // Atualiza badge
        const open  = data.filter(s => s.status === 'open').length;
        const badge = document.getElementById('badge-agendamentos');
        if (data.length > 0) { badge.textContent = open + ' de ' + data.length; badge.style.display = 'inline'; }
        else badge.style.display = 'none';
    })
    .catch(() => {
        document.getElementById('schedules-list').innerHTML = '<div class="text-center text-muted py-3"><small>Erro ao carregar.</small></div>';
    });
}

function avatarHtml(name, avatar, size) {
    size = size || 28;
    var style = 'width:' + size + 'px;height:' + size + 'px;font-size:' + Math.round(size * 0.4) + 'px;';
    if (avatar) return '<span class="dest-avatar" style="' + style + '"><img src="/' + avatar + '" alt="' + name + '"></span>';
    return '<span class="dest-avatar" style="' + style + '">' + name.charAt(0).toUpperCase() + '</span>';
}

function renderSchedules() {
    const list = document.getElementById('schedules-list');
    if (!schedules.length) {
        list.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-calendar-line fs-36 d-block mb-2 opacity-25"></i><small>Nenhum agendamento cadastrado.</small></div>';
        return;
    }
    list.innerHTML = schedules.map(function(s) {
        var isDone      = s.status === 'done';
        var isCreator   = s.created_by === AUTH_ID;
        var isRecipient = s.recipients.some(function(r) { return r.id === AUTH_ID; });
        var canComplete = !isDone && isRecipient;
        var canReopen   = isDone && (isCreator || s.completed_by === AUTH_ID);
        var canDelete   = isCreator;

        var typeBadge = s.type_name
            ? '<span class="badge fs-11 mb-2 d-inline-block" style="background:' + (s.type_color || '#1a3c5e') + ';color:#fff;">' + s.type_name + '</span>'
            : '';

        var metaPill = '<div class="sched-meta mb-2">'
            + '<span><i class="ri-calendar-line me-1"></i>' + formatDate(s.date) + '</span>'
            + '<span><i class="ri-time-line me-1"></i>' + (s.time ? s.time.slice(0,5) : '—') + '</span>'
            + '</div>';

        var description = s.description ? '<p class="text-muted fs-13 mb-2">' + s.description + '</p>' : '';

        var recipientsList = '<div class="mt-3 pt-2 border-top d-flex align-items-center flex-wrap gap-2">'
            + '<small class="text-muted fs-12">Destinatários:</small>'
            + s.recipients.map(function(r) {
                return '<span class="d-inline-flex align-items-center gap-1">'
                    + avatarHtml(r.name, r.avatar || '')
                    + '<span class="fs-12">' + r.name + '</span></span>';
            }).join('<span class="text-muted mx-1">|</span>')
            + '</div>';

        var createdInfo = '<div class="mt-2 pt-2 bg-light px-2 py-1 rounded d-flex align-items-center justify-content-end flex-wrap gap-3">'
            + '<small class="text-muted fs-11"><i class="ri-user-add-line me-1"></i>Criado por <strong>' + s.created_by_name + '</strong></small>'
            + (isDone && s.completed_by_name ? '<small class="text-success fs-11"><i class="ri-check-double-line me-1"></i>Concluído por <strong>' + s.completed_by_name + '</strong></small>' : '')
            + '</div>';

        var actions = ''
            + (canComplete ? '<button onclick="completeSchedule(' + s.id + ')" class="btn btn-sm btn-success"><i class="ri-check-double-line me-1"></i>Concluir</button>' : '')
            + (canReopen   ? '<button onclick="reopenSchedule(' + s.id + ')" class="btn btn-sm btn-warning"><i class="ri-restart-line me-1"></i>Reabrir</button>' : '')
            + (canDelete   ? '<button onclick="deleteSchedule(' + s.id + ')" class="btn btn-sm btn-danger"><i class="ri-delete-bin-line me-1"></i>Remover</button>' : '');

        return '<div class="schedule-item status-' + (isDone ? 'done' : 'open') + ' mb-3">'
            + '<div class="sched-side-bar"><span>' + (isDone ? 'CONCLUÍDO' : 'ABERTO') + '</span></div>'
            + '<div class="sched-body">'
            + '<div class="d-flex align-items-start justify-content-between gap-3">'
            + '<div class="flex-grow-1 min-w-0">'
            + (actions ? '<div class="float-end d-flex gap-1 ms-2">' + actions + '</div>' : '')
            + typeBadge + metaPill
            + '<p class="fw-bold fs-14 mb-1' + (isDone ? ' text-decoration-line-through text-muted' : '') + '">' + s.title + '</p>'
            + description + recipientsList + createdInfo
            + '</div></div></div></div>';
    }).join('');
}

// ════════════════════════════════════════════════════════════════════════════
// ANOTAÇÕES
// ════════════════════════════════════════════════════════════════════════════
document.getElementById('tab-anotacoes-btn').addEventListener('shown.bs.tab', () => loadNotes());

function initNoteFlatpickr() {
    if (!notesFpDate) {
        notesFpDate = flatpickr('#note_date', {
            locale: 'pt', dateFormat: 'Y-m-d',
            altInput: true, altFormat: 'd/m/Y', allowInput: true,
        });
    }
    if (!notesFpTime) {
        notesFpTime = flatpickr('#note_time', {
            enableTime: true, noCalendar: true,
            dateFormat: 'H:i', time_24hr: true, allowInput: true,
        });
        document.getElementById('note_time').addEventListener('click', () => notesFpTime.open());
    }
}

document.getElementById('btn-new-note').addEventListener('click', function () {
    editingNoteId = null;
    var titleEl = document.getElementById('note_modal_title');
    titleEl.innerHTML = '<i class="ri-sticky-note-line fs-18"></i> Nova Anotação';
    document.getElementById('note_content').value = '';
    document.getElementById('note_error').classList.add('d-none');
    initNoteFlatpickr();
    notesFpDate.setDate(new Date(), true);
    notesFpTime.setDate(new Date(), true);
    new bootstrap.Modal(document.getElementById('noteModal')).show();
});

function editNote(id, content, notedAt) {
    editingNoteId = id;
    var titleEl = document.getElementById('note_modal_title');
    titleEl.innerHTML = '<i class="ri-edit-line fs-18"></i> Editar Anotação';
    document.getElementById('note_content').value = content;
    document.getElementById('note_error').classList.add('d-none');
    initNoteFlatpickr();
    var parts = notedAt.split(' ');
    notesFpDate.setDate(parts[0], true);
    notesFpTime.setDate(parts[1] ? parts[1].slice(0,5) : '00:00', true);
    new bootstrap.Modal(document.getElementById('noteModal')).show();
}

document.getElementById('btn_save_note').addEventListener('click', function () {
    var content = document.getElementById('note_content').value.trim();
    var errEl   = document.getElementById('note_error');
    errEl.classList.add('d-none');

    var date = notesFpDate && notesFpDate.selectedDates[0] ? notesFpDate.formatDate(notesFpDate.selectedDates[0], 'Y-m-d') : '';
    var time = notesFpTime && notesFpTime.selectedDates[0] ? notesFpTime.formatDate(notesFpTime.selectedDates[0], 'H:i') : document.getElementById('note_time').value;

    if (!content || !date || !time) {
        errEl.textContent = 'Preencha a data, hora e o conteúdo.';
        errEl.classList.remove('d-none');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

    var isEdit = editingNoteId !== null;
    var url    = isEdit ? '/processos/anotacoes/' + editingNoteId : '/processos/anotacoes';
    var method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ process_id: PROCESS_ID, content: content, noted_at: date + ' ' + time + ':00' }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
            showToast('success', isEdit ? 'Anotação atualizada!' : 'Anotação registrada!');
            loadNotes();
        } else if (data.errors) {
            errEl.textContent = Object.values(data.errors)[0][0];
            errEl.classList.remove('d-none');
        }
    })
    .catch(() => { errEl.textContent = 'Erro ao salvar.'; errEl.classList.remove('d-none'); })
    .finally(() => { this.disabled = false; this.innerHTML = '<i class="ri-save-line me-1"></i> Salvar'; });
});

function loadNotes() {
    var list = document.getElementById('notes-list');
    list.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
    fetch('/processos/' + PROCESS_ID + '/anotacoes', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(function(notes) {
        renderNotes(notes);
        var badge = document.getElementById('badge-anotacoes');
        if (notes.length > 0) { badge.textContent = notes.length; badge.style.display = 'inline'; }
        else badge.style.display = 'none';
    })
    .catch(() => {
        document.getElementById('notes-list').innerHTML = '<div class="text-center text-muted py-3"><small>Erro ao carregar anotações.</small></div>';
    });
}

function renderNotes(notes) {
    var list = document.getElementById('notes-list');
    if (!notes.length) {
        list.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-sticky-note-line fs-36 d-block mb-2 opacity-25"></i><small>Nenhuma anotação registrada.</small></div>';
        return;
    }

    // Agrupa por data
    var grouped = {};
    notes.forEach(function(n) {
        var d = n.noted_at.slice(0, 10);
        if (!grouped[d]) grouped[d] = [];
        grouped[d].push(n);
    });

    var months = ['JAN','FEV','MAR','ABR','MAI','JUN','JUL','AGO','SET','OUT','NOV','DEZ'];
    var html = '';

    Object.keys(grouped).forEach(function(date) {
        var parts = date.split('-');
        var dateLabel = parts[2] + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];

        html += '<div class="mb-4">'
            + '<div class="d-flex align-items-center gap-3 mb-3">'
            + '<span class="note-date-badge"><i class="ri-calendar-line"></i>' + dateLabel + '</span>'
            + '<div class="flex-grow-1" style="height:1px;background:#e9ecef;"></div>'
            + '</div>';

        grouped[date].forEach(function(n) {
            var isOwner = n.user_id === AUTH_ID;
            var time    = n.noted_at.slice(11, 16);
            var avatarEl = n.user_avatar
                ? '<img src="/' + n.user_avatar + '" alt="' + n.user_name + '">'
                : n.user_name.charAt(0).toUpperCase();

            // Escapa conteúdo para atributo HTML
            var contentForAttr = n.content
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")
                .replace(/\n/g, '\\n')
                .replace(/\r/g, '');

            var noteActions = isOwner
                ? '<div class="d-flex gap-1 flex-shrink-0">'
                    + '<button onclick="editNote(' + n.id + ', \'' + contentForAttr + '\', \'' + n.noted_at + '\')" class="btn btn-sm btn-soft-primary"><i class="ri-edit-line me-1"></i>Editar</button>'
                    + '<button onclick="deleteNote(' + n.id + ')" class="btn btn-sm btn-soft-danger"><i class="ri-delete-bin-line me-1"></i>Remover</button>'
                    + '</div>'
                : '';

            html += '<div class="note-card mb-3">'
                + '<div class="d-flex align-items-start gap-3">'
                + '<div class="note-avatar">' + avatarEl + '</div>'
                + '<div class="flex-grow-1 min-w-0">'
                + '<div class="d-flex align-items-center justify-content-between gap-2 mb-1">'
                + '<div class="d-flex align-items-center gap-2">'
                + '<span class="fw-semibold fs-13">' + n.user_name + '</span>'
                + '<span class="badge bg-light text-muted fs-11"><i class="ri-time-line me-1"></i>' + time + '</span>'
                + '</div>'
                + noteActions
                + '</div>'
                + '<p class="mb-0 fs-13 text-muted" style="white-space:pre-wrap;">' + n.content + '</p>'
                + '</div></div></div>';
        });

        html += '</div>';
    });

    list.innerHTML = html;
}

function deleteNote(id) {
    showActionModal({
        title: 'Remover anotação', message: 'Esta ação não pode ser desfeita.',
        iconBg: 'bg-danger-subtle text-danger', iconEl: '<i class="ri-delete-bin-line"></i>',
        btnClass: 'btn-danger', btnLabel: 'Sim, remover',
        onConfirm: function() {
            fetch('/processos/anotacoes/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(r => r.json())
            .then(function(data) {
                if (data.success) { showToast('success', 'Anotação removida!'); loadNotes(); }
                else showToast('error', data.message || 'Sem permissão.');
            });
        },
    });
}

// ════════════════════════════════════════════════════════════════════════════
// ABA MOVIMENTAÇÕES (DataJud)
// ════════════════════════════════════════════════════════════════════════════

document.getElementById('tab-andamento-btn').addEventListener('shown.bs.tab', function () {
    // Carrega movimentações já salvas ao entrar na aba
    loadMovements(false);
});

document.getElementById('btn-sync-datajud').addEventListener('click', function () {
    syncMovements();
});

function syncMovements() {
    var btn  = document.getElementById('btn-sync-datajud');
    var list = document.getElementById('movements-list');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Consultando DataJud...';
    list.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div><p class="text-muted mt-2 fs-13">Consultando DataJud...</p></div>';

    fetch('/processos/' + PROCESS_ID + '/movimentacoes/sync', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) {
            var msg = data.synced > 0
                ? data.synced + ' nova(s) movimentação(ões) adicionada(s).'
                : 'Já estava atualizado.';
            showToast('success', 'DataJud sincronizado. ' + msg);
            renderMovements(data.movements, data.process_data);
            updateMovBadge(data.total);
            updateLastSynced(data.last_synced_at);
        } else {
            list.innerHTML = '<div class="alert alert-warning d-flex gap-2 align-items-start"><i class="ri-information-line fs-18 flex-shrink-0 mt-1"></i><div><strong>Não foi possível consultar o DataJud.</strong><br><small>' + (data.message || 'Verifique o número do processo.') + '</small></div></div>';
        }
    })
    .catch(function() {
        list.innerHTML = '<div class="text-center text-muted py-3"><small>Erro ao consultar DataJud.</small></div>';
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-refresh-line me-1"></i> Sincronizar DataJud';
    });
}

function loadMovements(showSpinner) {
    if (showSpinner) {
        document.getElementById('movements-list').innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
    }

    fetch('/processos/' + PROCESS_ID + '/movimentacoes', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.movements && data.movements.length) {
            renderMovements(data.movements, null);
            updateMovBadge(data.movements.length);
        }
        updateLastSynced(data.last_synced_at || null);
        // Se vazio, mantém o estado inicial (instrução para sincronizar)
    })
    .catch(function() {});
}

function updateMovBadge(total) {
    var badge = document.getElementById('badge-movimentacoes');
    if (total > 0) {
        badge.textContent = total;
        badge.style.display = 'inline';
    }
}

function updateLastSynced(lastSyncedAt) {
    var el = document.getElementById('mov-last-synced');
    if (!el) return;
    if (!lastSyncedAt) {
        el.textContent = 'Nunca sincronizado';
        el.className = 'fs-12 text-muted';
        return;
    }
    var d = new Date(lastSyncedAt);
    var pad = n => String(n).padStart(2, '0');
    var formatted = pad(d.getDate()) + '/' + pad(d.getMonth()+1) + '/' + d.getFullYear()
        + ' às ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    el.textContent = 'Última sincronização: ' + formatted;
    el.className = 'fs-12 text-muted';
}

function renderMovements(movements, processData) {
    var list = document.getElementById('movements-list');

    if (!movements || !movements.length) {
        list.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-time-line fs-36 d-block mb-2 opacity-25"></i><p>Nenhuma movimentação encontrada.</p></div>';
        return;
    }

    // Info do processo do DataJud (classe, assunto, tribunal)
    if (processData) {
        var infoEl = document.getElementById('mov-process-data');
        var parts = [];
        if (processData.tribunal)        parts.push(processData.tribunal);
        if (processData.classe)          parts.push(processData.classe);
        if (processData.orgao_julgador)  parts.push(processData.orgao_julgador);
        if (parts.length) infoEl.textContent = parts.join(' · ');
    }

    // Agrupa por data
    var grouped = {};
    movements.forEach(function(m) {
        var d = (m.movement_date || '').slice(0, 10);
        if (!grouped[d]) grouped[d] = [];
        grouped[d].push(m);
    });

    var months = ['JAN','FEV','MAR','ABR','MAI','JUN','JUL','AGO','SET','OUT','NOV','DEZ'];
    var html = '<div class="movements-timeline">';

    Object.keys(grouped).forEach(function(date) {
        var parts = date.split('-');
        var dateLabel = parts[2] + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];

        html += '<div class="mb-4">'
            + '<div class="d-flex align-items-center gap-3 mb-3">'
            + '<span class="mov-date-badge"><i class="ri-calendar-check-line me-1"></i>' + dateLabel + '</span>'
            + '<div class="flex-grow-1" style="height:1px;background:#e9ecef;"></div>'
            + '</div>';

        grouped[date].forEach(function(m) {
            html += '<div class="d-flex gap-3 mb-2 align-items-start">'
                + '<div class="flex-shrink-0 mt-1">'
                + '<div style="width:10px;height:10px;border-radius:50%;background:var(--brand-primary);margin-top:4px;"></div>'
                + '</div>'
                + '<div class="flex-grow-1 pb-2 border-bottom">'
                + '<p class="mb-0 fs-13">' + (m.description || '') + '</p>'
                + (m.code ? '<small class="text-muted">Código: ' + m.code + '</small>' : '')
                + '</div>'
                + '</div>';
        });

        html += '</div>';
    });

    html += '</div>'
        + '<style>'
        + '.movements-timeline { position:relative; }'
        + '.mov-date-badge { display:inline-flex;align-items:center;gap:6px;background:#f3f4f6;color:#495057;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;letter-spacing:.04em; }'
        + '</style>';

    list.innerHTML = html;
}

// ════════════════════════════════════════════════════════════════════════════
// ABA PUBLICAÇÕES (Escavador)
// ════════════════════════════════════════════════════════════════════════════
var PUBLICACOES_ENABLED = {{ !empty($currentTenant->publicacoes_enabled) ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function () {
    // Exibe ou oculta a aba conforme permissão do tenant
    if (PUBLICACOES_ENABLED) {
        document.getElementById('tab-publicacoes-nav').style.display = '';
    }
});

document.getElementById('tab-publicacoes-btn').addEventListener('shown.bs.tab', function () {
    loadPublications(false);
});

document.getElementById('btn-sync-publicacoes').addEventListener('click', function () {
    syncPublications();
});

function syncPublications() {
    var btn  = document.getElementById('btn-sync-publicacoes');
    var list = document.getElementById('publications-list');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Consultando...';
    list.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div><p class="text-muted mt-2 fs-13">Consultando Escavador...</p></div>';

    fetch('/processos/' + PROCESS_ID + '/publicacoes/sync', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) {
            var msg = data.synced > 0 ? data.synced + ' nova(s) publicação(ões) encontrada(s).' : 'Nenhuma novidade.';
            showToast('success', 'Escavador consultado. ' + msg);
            renderPublications(data.publications);
            updatePubBadge(data.total);
            updateUsageBar(data.usage);
        } else if (data.error === 'limit_reached') {
            list.innerHTML = '<div class="alert alert-warning d-flex gap-2 align-items-start"><i class="ri-alert-line fs-18 flex-shrink-0 mt-1"></i><div><strong>Limite mensal atingido.</strong><br><small>' + data.message + '</small></div></div>';
            updateUsageBar(data.usage);
        } else {
            list.innerHTML = '<div class="alert alert-danger d-flex gap-2 align-items-start"><i class="ri-error-warning-line fs-18 flex-shrink-0 mt-1"></i><div><strong>Erro ao consultar Escavador.</strong><br><small>' + (data.message || 'Tente novamente.') + '</small></div></div>';
        }
    })
    .catch(function() {
        list.innerHTML = '<div class="text-center text-muted py-3"><small>Erro inesperado. Tente novamente.</small></div>';
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-refresh-line me-1"></i> Buscar publicações';
    });
}

function loadPublications(showSpinner) {
    if (showSpinner) {
        document.getElementById('publications-list').innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
    }
    fetch('/processos/' + PROCESS_ID + '/publicacoes', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.publications && data.publications.length) {
            renderPublications(data.publications);
            updatePubBadge(data.publications.length);
        }
        if (data.usage) updateUsageBar(data.usage);
    })
    .catch(function() {});
}

function updatePubBadge(total) {
    var badge = document.getElementById('badge-publicacoes');
    if (total > 0) { badge.textContent = total; badge.style.display = 'inline'; }
}

function updateUsageBar(usage) {
    if (!usage) return;
    var bar  = document.getElementById('pub-usage-bar');
    var text = document.getElementById('pub-usage-text');
    var prog = document.getElementById('pub-usage-progress');
    var label = document.getElementById('pub-usage-label');

    bar.style.display = 'block';

    var creditsText = usage.total_credits + ' crédito(s) usados em ' + usage.month;
    if (usage.limit > 0) creditsText += ' / limite: ' + usage.limit;
    text.textContent = creditsText;
    if (label) label.textContent = usage.total_queries + ' consulta(s) este mês';

    var pct = usage.limit_percent || 0;
    prog.style.width = Math.min(pct, 100) + '%';
    prog.className = 'progress-bar ' + (pct >= 90 ? 'bg-danger' : pct >= 70 ? 'bg-warning' : 'bg-success');
}

function renderPublications(publications) {
    var list = document.getElementById('publications-list');
    if (!publications || !publications.length) {
        list.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-newspaper-line fs-36 d-block mb-2 opacity-25"></i><p>Nenhuma publicação encontrada para este processo.</p></div>';
        return;
    }

    // Agrupa por data
    var grouped = {};
    publications.forEach(function(p) {
        var d = (p.publication_date || '').slice(0, 10);
        if (!grouped[d]) grouped[d] = [];
        grouped[p.publication_date ? d : 'sem-data'].push(p);
    });

    var months = ['JAN','FEV','MAR','ABR','MAI','JUN','JUL','AGO','SET','OUT','NOV','DEZ'];
    var html = '';

    Object.keys(grouped).sort().reverse().forEach(function(date) {
        var dateLabel = '—';
        if (date !== 'sem-data') {
            var parts = date.split('-');
            if (parts.length === 3) dateLabel = parts[2] + ' ' + months[parseInt(parts[1])-1] + ' ' + parts[0];
        }

        html += '<div class="mb-4">'
            + '<div class="d-flex align-items-center gap-3 mb-3">'
            + '<span style="display:inline-flex;align-items:center;gap:6px;background:#f3f4f6;color:#495057;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;">'
            + '<i class="ri-newspaper-line"></i>' + dateLabel + '</span>'
            + '<div class="flex-grow-1" style="height:1px;background:#e9ecef;"></div>'
            + '</div>';

        grouped[date].forEach(function(p) {
            var meta = [];
            if (p.diario)   meta.push('<span class="badge bg-light text-muted border">' + p.diario + '</span>');
            if (p.caderno)  meta.push('<span class="badge bg-light text-muted border">' + p.caderno + '</span>');
            if (p.source)   meta.push('<small class="text-muted">' + p.source + '</small>');

            html += '<div class="card border mb-3">'
                + '<div class="card-body py-3">'
                + (meta.length ? '<div class="d-flex flex-wrap gap-1 mb-2">' + meta.join('') + '</div>' : '')
                + '<p class="mb-0 fs-13 text-muted" style="white-space:pre-wrap;line-height:1.7;">' + (p.content || '') + '</p>'
                + '</div></div>';
        });

        html += '</div>';
    });

    list.innerHTML = html;
}

function formatDate(d) {
    if (!d) return '—';
    var p = d.split('-');
    return p[2] + '/' + p[1] + '/' + p[0];
}
</script>
@endpush

@endsection
