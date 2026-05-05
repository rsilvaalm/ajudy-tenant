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
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active py-3" data-bs-toggle="tab" href="#tab-info" role="tab">
                    <i class="ri-information-line me-1"></i><span class="d-none d-sm-inline">Informações</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3" data-bs-toggle="tab" href="#tab-agenda" role="tab" id="tab-agenda-btn">
                    <i class="ri-calendar-line me-1"></i><span class="d-none d-sm-inline">Agendamentos</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-andamento" role="tab">
                    <i class="ri-git-commit-line me-1"></i><span class="d-none d-sm-inline">Andamento</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-despesas" role="tab">
                    <i class="ri-money-dollar-circle-line me-1"></i><span class="d-none d-sm-inline">Despesas</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-3 text-muted" data-bs-toggle="tab" href="#tab-honorarios" role="tab">
                    <i class="ri-hand-coin-line me-1"></i><span class="d-none d-sm-inline">Honorários</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body tab-content p-4">

        {{-- Informações --}}
        <div class="tab-pane active" id="tab-info" role="tabpanel">
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

        <div class="tab-pane" id="tab-andamento" role="tabpanel">
            <div class="text-center text-muted py-5"><i class="ri-git-commit-line fs-48 d-block mb-2 opacity-25"></i><p class="mb-0">Em desenvolvimento.</p></div>
        </div>
        <div class="tab-pane" id="tab-despesas" role="tabpanel">
            <div class="text-center text-muted py-5"><i class="ri-money-dollar-circle-line fs-48 d-block mb-2 opacity-25"></i><p class="mb-0">Em desenvolvimento.</p></div>
        </div>
        <div class="tab-pane" id="tab-honorarios" role="tabpanel">
            <div class="text-center text-muted py-5"><i class="ri-hand-coin-line fs-48 d-block mb-2 opacity-25"></i><p class="mb-0">Em desenvolvimento.</p></div>
        </div>

    </div>
</div>

{{-- Modal: Novo Agendamento --}}
<div class="modal fade" id="newScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--brand-primary);">
                <h5 class="modal-title text-white"><i class="ri-calendar-add-line me-2"></i> Novo Agendamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                        <input type="text" id="sched_date" class="form-control flatpickr-sched" placeholder="DD/MM/AAAA" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hora <span class="text-danger">*</span></label>
                        <input type="text" id="sched_time" class="form-control" placeholder="HH:MM" autocomplete="off">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Destinatários <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" id="sched_recipient_search" class="form-control" placeholder="Buscar usuário..." autocomplete="off">
                            <div id="sched_recipient_results" class="position-absolute w-100 bg-white border rounded shadow-lg"
                                 style="top:100%;z-index:999;display:none;max-height:180px;overflow-y:auto;"></div>
                        </div>
                        <div id="sched_recipients_selected" class="d-flex flex-wrap gap-1 mt-2"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea id="sched_description" class="form-control" rows="3" placeholder="Detalhes do agendamento..."></textarea>
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
/* ── Card do agendamento ─────────────────────────────────────────────────── */
.schedule-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: box-shadow .15s;
    display: flex;
}
.schedule-item:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); }

/* Barra lateral colorida com texto vertical */
.sched-side-bar {
    width: 28px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.sched-side-bar span {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    transform: rotate(180deg);
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
}

/* Status: aberto = amarelo âmbar; concluído = verde */
.schedule-item.status-open  .sched-side-bar { background: #f7b731; }
.schedule-item.status-open  .sched-side-bar span { color: #fff; }
.schedule-item.status-done  .sched-side-bar { background: #0ab39c; }
.schedule-item.status-done  .sched-side-bar span { color: #fff; }

/* Corpo */
.sched-body { flex: 1; padding: 14px 16px; min-width: 0; }

/* Pill de meta-info */
.sched-meta {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    background: #f3f4f6;
    border-radius: 6px;
    padding: 4px 12px;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 10px;
}

/* Avatar do destinatário */
.dest-avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--brand-primary);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}
.dest-avatar img { width: 100%; height: 100%; object-fit: cover; }

/* Ações */
.sched-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: flex-end;
    justify-content: flex-start;
    flex-shrink: 0;
    padding: 14px 14px 14px 0;
}

/* Campo hora sem seta nativa */
#sched_time { cursor: pointer; }
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

// ── Carrega ao clicar na aba ──────────────────────────────────────────────
document.getElementById('tab-agenda-btn').addEventListener('shown.bs.tab', () => loadSchedules());

// ── Inicializa modal ──────────────────────────────────────────────────────
document.getElementById('newScheduleModal').addEventListener('show.bs.modal', function () {
    // Datepicker
    if (!document.getElementById('sched_date')._flatpickr) {
        flatpickr('#sched_date', {
            locale: 'pt', dateFormat: 'Y-m-d',
            altInput: true, altFormat: 'd/m/Y', allowInput: true,
        });
    }

    // Timepicker — abre ao clicar em qualquer parte do campo
    if (!document.getElementById('sched_time')._flatpickr) {
        const fpTime = flatpickr('#sched_time', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            allowInput: true,
        });
        // Garante abertura ao clicar no campo e no altInput
        document.getElementById('sched_time').addEventListener('click', () => fpTime.open());
        if (fpTime.altInput) fpTime.altInput.addEventListener('click', () => fpTime.open());
    }

    if (document.getElementById('sched_type_id').options.length <= 1) loadScheduleTypes();
});

// Maiúsculas
document.querySelectorAll('.sched-upper').forEach(el => {
    el.addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
});

// ── Tipos ─────────────────────────────────────────────────────────────────
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

// ── Destinatários ─────────────────────────────────────────────────────────
document.getElementById('sched_recipient_search').addEventListener('input', function () {
    clearTimeout(recipientSearchTimeout);
    const q = this.value.trim();
    if (!q) { document.getElementById('sched_recipient_results').style.display = 'none'; return; }
    recipientSearchTimeout = setTimeout(() => searchRecipients(q), 300);
});

function searchRecipients(q) {
    fetch(`/agendamentos/buscar-usuarios?q=${encodeURIComponent(q)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(users => {
        const c = document.getElementById('sched_recipient_results');
        const filtered = users.filter(u => !selectedRecipients.some(r => r.id === u.id));
        c.innerHTML = !filtered.length
            ? '<div class="p-2 text-muted text-center fs-13">Nenhum usuário encontrado.</div>'
            : filtered.map(u => `
                <div class="p-2 px-3 border-bottom d-flex align-items-center gap-2" style="cursor:pointer;"
                     onclick="addRecipient(${u.id}, '${u.name.replace(/'/g,"\\'")}', '${u.avatar || ''}')">
                    <span class="dest-avatar">${u.avatar
                        ? `<img src="/${u.avatar}">`
                        : u.name.charAt(0).toUpperCase()
                    }</span>
                    <span class="fs-13">${u.name}</span>
                </div>`).join('');
        c.style.display = 'block';
    });
}

function addRecipient(id, name, avatar) {
    if (selectedRecipients.some(r => r.id === id)) return;
    selectedRecipients.push({ id, name, avatar });
    renderRecipients();
    document.getElementById('sched_recipient_search').value = '';
    document.getElementById('sched_recipient_results').style.display = 'none';
}

function removeRecipient(id) {
    selectedRecipients = selectedRecipients.filter(r => r.id !== id);
    renderRecipients();
}

function renderRecipients() {
    document.getElementById('sched_recipients_selected').innerHTML =
        selectedRecipients.map(r => `
            <span class="badge bg-primary-subtle text-primary d-inline-flex align-items-center gap-1 px-2 py-1">
                <span class="dest-avatar" style="width:20px;height:20px;font-size:9px;">${r.avatar
                    ? `<img src="/${r.avatar}">`
                    : r.name.charAt(0).toUpperCase()
                }</span>
                ${r.name}
                <button type="button" onclick="removeRecipient(${r.id})"
                        class="btn btn-link p-0 text-primary" style="line-height:1;">
                    <i class="ri-close-line fs-12"></i>
                </button>
            </span>`).join('');
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('#sched_recipient_search') && !e.target.closest('#sched_recipient_results'))
        document.getElementById('sched_recipient_results').style.display = 'none';
});

// ── Abre modal limpo ──────────────────────────────────────────────────────
document.getElementById('btn-new-schedule').addEventListener('click', function () {
    ['sched_title','sched_description'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('sched_type_id').value = '';
    document.getElementById('sched_error').classList.add('d-none');
    selectedRecipients = [];
    renderRecipients();
    const fpDate = document.getElementById('sched_date')._flatpickr;
    const fpTime = document.getElementById('sched_time')._flatpickr;
    if (fpDate) fpDate.clear();
    if (fpTime) fpTime.clear();
    new bootstrap.Modal(document.getElementById('newScheduleModal')).show();
});

// ── Salva ─────────────────────────────────────────────────────────────────
document.getElementById('btn_save_schedule').addEventListener('click', function () {
    const title  = document.getElementById('sched_title').value.trim();
    const typeId = document.getElementById('sched_type_id').value;
    const desc   = document.getElementById('sched_description').value;
    const errEl  = document.getElementById('sched_error');

    const fpDate = document.getElementById('sched_date')._flatpickr;
    const fpTime = document.getElementById('sched_time')._flatpickr;
    const date   = fpDate && fpDate.selectedDates[0] ? fpDate.formatDate(fpDate.selectedDates[0], 'Y-m-d') : '';
    const time   = fpTime && fpTime.selectedDates[0] ? fpTime.formatDate(fpTime.selectedDates[0], 'H:i') : document.getElementById('sched_time').value;

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

// ── Modal de confirmação ──────────────────────────────────────────────────
function showActionModal({ title, message, iconBg, iconEl, btnClass, btnLabel, onConfirm }) {
    document.getElementById('sched_action_title').textContent   = title;
    document.getElementById('sched_action_message').textContent = message;
    const wrap = document.getElementById('sched_action_icon_wrap');
    wrap.className = `avatar-title rounded-circle fs-36 ${iconBg}`;
    wrap.innerHTML = iconEl;
    const btn = document.getElementById('sched_action_confirm');
    btn.className   = `btn ${btnClass}`;
    btn.textContent = btnLabel;
    pendingAction   = onConfirm;
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
        onConfirm: () => patchSchedule(`/agendamentos/${id}/concluir`, 'Agendamento concluído!'),
    });
}

function reopenSchedule(id) {
    showActionModal({
        title: 'Reabrir agendamento', message: 'Deseja reabrir este agendamento?',
        iconBg: 'bg-warning-subtle text-warning', iconEl: '<i class="ri-restart-line"></i>',
        btnClass: 'btn-warning', btnLabel: 'Sim, reabrir',
        onConfirm: () => patchSchedule(`/agendamentos/${id}/reabrir`, 'Agendamento reaberto!'),
    });
}

function deleteSchedule(id) {
    showActionModal({
        title: 'Remover agendamento', message: 'Esta ação não pode ser desfeita.',
        iconBg: 'bg-danger-subtle text-danger', iconEl: '<i class="ri-delete-bin-line"></i>',
        btnClass: 'btn-danger', btnLabel: 'Sim, remover',
        onConfirm: () => {
            fetch(`/agendamentos/${id}`, {
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

// ── Carrega e renderiza ───────────────────────────────────────────────────
function loadSchedules() {
    const list = document.getElementById('schedules-list');
    list.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';

    fetch(`/agendamentos/processo/${PROCESS_ID}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => { schedules = data; renderSchedules(); })
    .catch(() => { document.getElementById('schedules-list').innerHTML = '<div class="text-center text-muted py-3"><small>Erro ao carregar.</small></div>'; });
}

function avatarHtml(name, avatar, size = 28) {
    const style = `width:${size}px;height:${size}px;font-size:${Math.round(size*0.4)}px;`;
    if (avatar) {
        return `<span class="dest-avatar" style="${style}"><img src="/${avatar}" alt="${name}"></span>`;
    }
    return `<span class="dest-avatar" style="${style}">${name.charAt(0).toUpperCase()}</span>`;
}

function renderSchedules() {
    const list = document.getElementById('schedules-list');

    if (!schedules.length) {
        list.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="ri-calendar-line fs-36 d-block mb-2 opacity-25"></i>
                <small>Nenhum agendamento cadastrado.</small>
            </div>`;
        return;
    }

    list.innerHTML = schedules.map(s => {
        const isDone      = s.status === 'done';
        const isCreator   = s.created_by === AUTH_ID;
        const isRecipient = s.recipients.some(r => r.id === AUTH_ID);
        const canComplete = !isDone && isRecipient;
        const canReopen   = isDone && (isCreator || s.completed_by === AUTH_ID);
        const canDelete   = isCreator;

        const statusClass = isDone ? 'done' : 'open';
        const statusLabel = isDone ? 'CONCLUÍDO' : 'ABERTO';
        const bordercolor = isDone ? 'border-success' : 'border-warning';

        // Badge de tipo
        const typeBadge = s.type_name
            ? `<span class="badge fs-11 mb-2 d-inline-block" style="background:${s.type_color || '#1a3c5e'};color:#fff;">${s.type_name}</span>`
            : '';

        // Meta pill
        const metaPill = `
            <div class="sched-meta mb-2">
                <span><i class="ri-calendar-line me-1"></i>${formatDate(s.date)}</span>
                <span><i class="ri-time-line me-1"></i>${s.time ? s.time.slice(0,5) : '—'}</span>
            </div>`;

        // Descrição
        const description = s.description
            ? `<p class="text-muted fs-13 mb-2">${s.description}</p>`
            : '';

        // Destinatários com foto
        const recipients = `
            <div class="mt-3 pt-2 border-top d-flex align-items-center flex-wrap gap-3">
                <small class="text-muted fs-12">Destinatários:</small>
                ${s.recipients.map(r => `
                    <span class="d-inline-flex align-items-center gap-1">
                        ${avatarHtml(r.name, r.avatar || '')}
                        <span class="fs-12">${r.name}</span>
                    </span>`).join('<span class="text-muted mx-1"> </span>')}
            </div>`;

        // Info de conclusão
        const createdInfo = `
            <div class="mt-3 pt-2 bg-light p-2 rounded border-top d-flex align-items-center justify-content-end flex-wrap gap-3">
                <small class="text-muted fs-11">
                    <i class="ri-user-add-line me-1"></i>Criado por <strong>${s.created_by_name}</strong>
                </small>
                ${isDone && s.completed_by_name ? `
                <small class="text-success fs-11">
                    <i class="ri-check-double-line me-1"></i>Concluído por <strong>${s.completed_by_name}</strong>
                </small>` : ''}
            </div>`;

        // Botões de ação
        const actions = [
            canComplete ? `<button onclick="completeSchedule(${s.id})" class="btn btn-sm btn-success"><i class="ri-check-double-line me-1"></i>Concluir</button>`  : '',
            canReopen   ? `<button onclick="reopenSchedule(${s.id})"   class="btn btn-sm btn-warning"><i class="ri-restart-line me-1"></i>Reabrir</button>`        : '',
            canDelete   ? `<button onclick="deleteSchedule(${s.id})"   class="btn btn-sm btn-danger"><i class="ri-delete-bin-line me-1"></i>Remover</button>`      : '',
        ].filter(Boolean).join('');

        return `
        <div class="schedule-item ${bordercolor} status-${statusClass} mb-3">
            <div class="sched-side-bar"><span>${statusLabel}</span></div>
            <div class="sched-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="flex-grow-1 min-w-0">
                        ${actions ? `<div class="sched-actions float-end">${actions}</div>` : ''}
                        ${typeBadge}
                        ${metaPill}
                        <p class="fw-bold fs-14 mb-1 ${isDone ? 'text-decoration-line-through text-muted' : ''}">${s.title}</p>
                        ${description}
                        ${recipients}
                        ${createdInfo}
                        
                    </div>
                    
                </div>
            </div>
        </div>`;
    }).join('');
}

function formatDate(d) {
    if (!d) return '—';
    const [y, m, day] = d.split('-');
    return `${day}/${m}/${y}`;
}
</script>
@endpush

@endsection
