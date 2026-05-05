{{-- ── Modal de Atendimento Rápido ──────────────────────────────────────── --}}
<div class="modal fade" id="attendanceModal" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header align-items-center px-4 py-3"
                 style="background:var(--brand-primary);">
                <h5 class="modal-title text-white d-flex align-items-center gap-2 mb-0">
                    <i class="ri-customer-service-2-line fs-18"></i>
                    <span id="att_modal_title">Novo Atendimento</span>
                </h5>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <button type="button"
                            id="att_btn_minimize"
                            class="btn btn-link text-white p-0 d-none"
                            title="Minimizar" style="line-height:1;">
                        <i class="ri-subtract-line fs-20"></i>
                    </button>
                    <button type="button"
                            id="att_btn_close_modal"
                            class="btn btn-link text-white p-0"
                            title="Fechar" style="line-height:1;">
                        <i class="ri-close-line fs-20"></i>
                    </button>
                </div>
            </div>

            <div class="modal-body p-4">

                {{-- ── FASE 1 ───────────────────────────────────────────── --}}
                <div id="attendance-phase-1">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Data</label>
                            <input type="date" id="att_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Hora de início</label>
                            <input type="time" id="att_start_time" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-3">
                        <button type="button" class="btn btn-sm att-tab-btn active" data-tab="existing" style="min-width:140px;">
                            <i class="ri-user-search-line me-1"></i> Já é cliente
                        </button>
                        <button type="button" class="btn btn-sm att-tab-btn" data-tab="new" style="min-width:140px;">
                            <i class="ri-user-add-line me-1"></i> Não é cliente
                        </button>
                    </div>

                    <div id="tab-existing" class="att-tab-content">
                        <div class="position-relative">
                            <input type="text" id="att_search" class="form-control"
                                   placeholder="Digite o nome do cliente..." autocomplete="off">
                            <div id="att_search_results"
                                 class="position-absolute w-100 bg-white border rounded shadow-lg"
                                 style="top:100%;z-index:9999;display:none;max-height:250px;overflow-y:auto;"></div>
                        </div>
                        <div id="att_selected_client" class="mt-2 p-2 rounded d-none"
                             style="background:rgba(26,60,94,.08);border:1px solid rgba(26,60,94,.2);">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-semibold" id="att_selected_name"></span>
                                    <small class="text-muted d-block" id="att_selected_info"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0" id="att_clear_client">
                                    <i class="ri-close-line fs-16"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="tab-new" class="att-tab-content" style="display:none;">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" id="att_new_name" class="form-control"
                                       placeholder="NOME DO CLIENTE" style="text-transform:uppercase;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" id="att_new_email" class="form-control" placeholder="email@exemplo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefone / Celular</label>
                                <input type="text" id="att_new_phone" class="form-control" placeholder="(00) 00000-0000">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="btn_start_attendance" class="btn btn-primary w-100">
                            <i class="ri-play-circle-line me-1"></i> Iniciar atendimento
                        </button>
                    </div>
                </div>

                {{-- ── FASE 2 ───────────────────────────────────────────── --}}
                <div id="attendance-phase-2" style="display:none;">
                    <div class="d-flex align-items-center gap-3 p-3 rounded mb-3"
                         style="background:rgba(10,179,156,.08);border:1px solid rgba(10,179,156,.2);">
                        <i class="ri-checkbox-circle-line text-success fs-20"></i>
                        <div class="flex-grow-1">
                            <p class="fw-semibold mb-0" id="phase2_client_name"></p>
                            <small class="text-muted" id="phase2_time_info"></small>
                        </div>
                        <small id="att_autosave_status" class="text-muted" style="font-size:11px;white-space:nowrap;"></small>
                    </div>

                    {{-- Toolbar de formatação --}}
                    <div class="border border-bottom-0 rounded-top p-2 d-flex gap-1 flex-wrap"
                         style="background:#f8f9fa;" id="att_toolbar">
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="bold" title="Negrito">
                            <strong>B</strong>
                        </button>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="italic" title="Itálico">
                            <em>I</em>
                        </button>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="underline" title="Sublinhado">
                            <u>U</u>
                        </button>
                        <div class="vr mx-1"></div>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="insertUnorderedList" title="Lista">
                            <i class="ri-list-unordered"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="insertOrderedList" title="Lista numerada">
                            <i class="ri-list-ordered"></i>
                        </button>
                        <div class="vr mx-1"></div>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="justifyLeft" title="Alinhar esquerda">
                            <i class="ri-align-left"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="justifyCenter" title="Centralizar">
                            <i class="ri-align-center"></i>
                        </button>
                        <div class="vr mx-1"></div>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1 att-fmt" data-cmd="removeFormat" title="Limpar formatação">
                            <i class="ri-format-clear"></i>
                        </button>
                    </div>

                    {{-- Editor contenteditable --}}
                    <div id="att_editor"
                         contenteditable="true"
                         class="form-control rounded-0 rounded-bottom"
                         style="min-height:220px;max-height:360px;overflow-y:auto;outline:none;font-size:14px;line-height:1.6;"
                         data-placeholder="Descreva o atendimento, observações, próximos passos..."></div>

                    {{-- Campo hidden para submit --}}
                    <input type="hidden" id="att_notes">

                    <button type="button" id="btn_close_attendance" class="btn btn-success w-100 mt-3">
                        <i class="ri-check-double-line me-1"></i> Encerrar atendimento
                    </button>
                </div>

                {{-- ── FASE 3 ───────────────────────────────────────────── --}}
                <div id="attendance-phase-3" style="display:none;" class="text-center py-3">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title rounded-circle bg-success-subtle text-success fs-36">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">Atendimento encerrado!</h5>
                    <p class="text-muted mb-0" id="phase3_summary"></p>
                    <button type="button" class="btn btn-light mt-3" id="att_btn_finish">Fechar</button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ── Modal de alerta ──────────────────────────────────────────────────── --}}
<div class="modal fade" id="attendanceWarningModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title rounded-circle bg-warning-subtle text-warning fs-36">
                        <i class="ri-alarm-warning-line"></i>
                    </div>
                </div>
                <h5 class="mb-1">Atendimento em andamento</h5>
                <p class="text-muted mb-0">O que deseja fazer?</p>
            </div>
            <div class="modal-footer border-0 pt-0 flex-column gap-2 pb-4">
                <button class="btn btn-primary w-100" id="warn_continue">
                    <i class="ri-arrow-go-back-line me-1"></i> Voltar
                </button>
                <button class="btn btn-outline-secondary w-100" id="warn_minimize">
                    <i class="ri-subtract-line me-1"></i> Manter aberto
                </button>
                <button class="btn btn-success w-100 mt-2" id="warn_finish">
                    <i class="ri-check-double-line me-1"></i> Encerrar atendimento
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .att-tab-btn {
        background: transparent;
        border: 2px solid #dee2e6;
        color: #6c757d;
        font-weight: 500;
        transition: all .2s;
    }
    .att-tab-btn.active {
        background: var(--brand-primary);
        border-color: var(--brand-primary);
        color: #fff;
    }
    #att_search_results .att-result-item {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background .15s;
    }
    #att_search_results .att-result-item:hover { background: #f8f9fa; }
    #att_search_results .att-result-item:last-child { border-bottom: none; }

    /* Editor placeholder */
    #att_editor:empty::before {
        content: attr(data-placeholder);
        color: #adb5bd;
        pointer-events: none;
    }
    #att_editor:focus { box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb),.25); }

    /* Toolbar btn ativo */
    .att-fmt.active { background: #e9ecef; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let currentAttendanceId = null;
    let currentPhase        = 1;
    let currentClientType   = 'existing';
    let selectedClientId    = null;
    let searchTimeout       = null;
    let autosaveTimeout     = null;
    let isOpen              = false;

    const bsModal      = new bootstrap.Modal(document.getElementById('attendanceModal'));
    const warningModal = new bootstrap.Modal(document.getElementById('attendanceWarningModal'));
    const editor       = document.getElementById('att_editor');

    // ── Toolbar de formatação ─────────────────────────────────────────────
    document.querySelectorAll('.att-fmt').forEach(btn => {
        btn.addEventListener('mousedown', function (e) {
            e.preventDefault(); // evita tirar o foco do editor
            document.execCommand(this.dataset.cmd, false, null);
            editor.focus();
        });
    });

    // Autosave ao digitar no editor
    editor.addEventListener('input', function () {
        clearTimeout(autosaveTimeout);
        setAutosaveStatus('Salvando...');
        autosaveTimeout = setTimeout(() => saveNotes(), 1500);
    });

    // ── Verifica atendimento em aberto ao carregar ────────────────────────
    function checkCurrentAttendance() {
        fetch('/atendimento/atual', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data && data.id) {
                currentAttendanceId = data.id;
                isOpen              = true;
                currentPhase        = 2;
                document.getElementById('phase2_client_name').textContent = data.client_name;
                document.getElementById('phase2_time_info').textContent   =
                    'Iniciado às ' + data.start_time + ' — ' + formatDate(data.date);
                if (data.notes) editor.innerHTML = data.notes;
                document.getElementById('att_modal_title').textContent = 'Atendimento em andamento';
                document.getElementById('att_btn_minimize').classList.remove('d-none');
                updateTopbarBtn(true);
            } else {
                isOpen       = false;
                currentPhase = 1;
                updateTopbarBtn(false);
            }
        })
        .catch(() => {});
    }

    checkCurrentAttendance();

    // ── Autosave ──────────────────────────────────────────────────────────
    function saveNotes() {
        if (!currentAttendanceId) return;
        const notes = editor.innerHTML;
        document.getElementById('att_notes').value = notes;

        fetch(`/atendimento/${currentAttendanceId}/encerrar`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ notes, autosave: true }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const now = new Date();
                setAutosaveStatus('Salvo às ' + now.toTimeString().slice(0, 5));
                setTimeout(() => setAutosaveStatus(''), 3000);
            }
        })
        .catch(() => setAutosaveStatus('Erro ao salvar'));
    }

    function setAutosaveStatus(msg) {
        const el = document.getElementById('att_autosave_status');
        if (el) el.textContent = msg;
    }

    // ── Atualiza botão da topbar ──────────────────────────────────────────
    function updateTopbarBtn(hasOpen) {
        const btn  = document.getElementById('btn-attendance-topbar');
        const text = document.getElementById('btn-attendance-text');
        if (!btn || !text) return;
        if (hasOpen) {
            btn.classList.add('btn-attendance-active');
            text.textContent = 'Continuar atendimento';
        } else {
            btn.classList.remove('btn-attendance-active');
            text.textContent = 'Iniciar atendimento';
        }
    }

    // ── Abre modal ────────────────────────────────────────────────────────
    document.getElementById('btn-attendance-topbar').addEventListener('click', function () {
        showPhase(currentPhase);
        if (currentPhase === 1) resetPhase1();
        bsModal.show();
    });

    // ── Minimizar ─────────────────────────────────────────────────────────
    document.getElementById('att_btn_minimize').addEventListener('click', function () {
        bsModal.hide();
    });

    // ── Tentar fechar ─────────────────────────────────────────────────────
    document.getElementById('att_btn_close_modal').addEventListener('click', function () {
        if (isOpen && currentPhase === 2) {
            bsModal.hide();
            setTimeout(() => warningModal.show(), 300);
        } else {
            bsModal.hide();
        }
    });

    // ── Opções do alerta ──────────────────────────────────────────────────
    document.getElementById('warn_continue').addEventListener('click', function () {
        warningModal.hide();
        setTimeout(() => bsModal.show(), 300);
    });

    document.getElementById('warn_minimize').addEventListener('click', function () {
        warningModal.hide();
    });

    document.getElementById('warn_finish').addEventListener('click', function () {
        warningModal.hide();
        setTimeout(() => {
            bsModal.show();
            setTimeout(() => closeAttendance(), 300);
        }, 300);
    });

    // ── Fechar após fase 3 ────────────────────────────────────────────────
    document.getElementById('att_btn_finish').addEventListener('click', function () {
        bsModal.hide();
        isOpen              = false;
        currentAttendanceId = null;
        currentPhase        = 1;
        editor.innerHTML    = '';
        document.getElementById('att_modal_title').textContent = 'Novo Atendimento';
        document.getElementById('att_btn_minimize').classList.add('d-none');
        updateTopbarBtn(false);
    });

    // ── Reset fase 1 ──────────────────────────────────────────────────────
    function resetPhase1() {
        selectedClientId  = null;
        currentClientType = 'existing';
        switchTab('existing');
        document.getElementById('att_search').value    = '';
        document.getElementById('att_new_name').value  = '';
        document.getElementById('att_new_email').value = '';
        document.getElementById('att_new_phone').value = '';
        editor.innerHTML = '';
        document.getElementById('att_search_results').style.display = 'none';
        document.getElementById('att_selected_client').classList.add('d-none');

        const now = new Date();
        document.getElementById('att_date').value       = now.toISOString().split('T')[0];
        document.getElementById('att_start_time').value = now.toTimeString().slice(0, 5);
    }

    function showPhase(n) {
        [1, 2, 3].forEach(i => {
            document.getElementById('attendance-phase-' + i).style.display = i === n ? 'block' : 'none';
        });
        currentPhase = n;
        document.getElementById('att_btn_minimize').classList.toggle('d-none', n !== 2);
    }

    // ── Tabs ──────────────────────────────────────────────────────────────
    document.querySelectorAll('.att-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () { switchTab(this.dataset.tab); });
    });

    function switchTab(tab) {
        currentClientType = tab;
        document.querySelectorAll('.att-tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
        document.querySelectorAll('.att-tab-content').forEach(c => c.style.display = 'none');
        document.getElementById('tab-' + tab).style.display = 'block';
    }

    // ── Busca de clientes ─────────────────────────────────────────────────
    document.getElementById('att_search').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { document.getElementById('att_search_results').style.display = 'none'; return; }
        searchTimeout = setTimeout(() => searchClients(q), 300);
    });

    function searchClients(q) {
        fetch(`/atendimento/buscar-clientes?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(clients => {
            const container = document.getElementById('att_search_results');
            if (!clients.length) {
                container.innerHTML = '<div class="p-3 text-muted text-center fs-13">Nenhum cliente encontrado.</div>';
            } else {
                container.innerHTML = clients.map(c => `
                    <div class="att-result-item" data-id="${c.id}" data-name="${c.name}"
                         data-email="${c.email || ''}" data-phone="${c.mobile || c.phone || ''}">
                        <div class="fw-medium">${c.name}</div>
                        <small class="text-muted">${c.cpf ? 'CPF: '+c.cpf : ''} ${c.mobile || c.phone || ''}</small>
                    </div>
                `).join('');
                container.querySelectorAll('.att-result-item').forEach(item => {
                    item.addEventListener('click', function () {
                        selectClient(this.dataset.id, this.dataset.name, this.dataset.email, this.dataset.phone);
                    });
                });
            }
            container.style.display = 'block';
        });
    }

    function selectClient(id, name, email, phone) {
        selectedClientId = id;
        document.getElementById('att_search').value = '';
        document.getElementById('att_search_results').style.display = 'none';
        document.getElementById('att_selected_name').textContent = name;
        document.getElementById('att_selected_info').textContent = [email, phone].filter(Boolean).join(' · ');
        document.getElementById('att_selected_client').classList.remove('d-none');
    }

    document.getElementById('att_clear_client').addEventListener('click', function () {
        selectedClientId = null;
        document.getElementById('att_selected_client').classList.add('d-none');
        document.getElementById('att_search').value = '';
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#tab-existing'))
            document.getElementById('att_search_results').style.display = 'none';
    });

    // ── Iniciar atendimento ───────────────────────────────────────────────
    document.getElementById('btn_start_attendance').addEventListener('click', function () {
        const date      = document.getElementById('att_date').value;
        const startTime = document.getElementById('att_start_time').value;

        if (!date || !startTime) { showToast('warning', 'Informe a data e hora de início.'); return; }

        let payload = { client_type: currentClientType, date, start_time: startTime };

        if (currentClientType === 'existing') {
            if (!selectedClientId) { showToast('warning', 'Selecione um cliente.'); return; }
            payload.client_id = selectedClientId;
        } else {
            const name = document.getElementById('att_new_name').value.trim();
            if (!name) { showToast('warning', 'Informe o nome do cliente.'); return; }
            payload.client_name  = name;
            payload.client_email = document.getElementById('att_new_email').value;
            payload.client_phone = document.getElementById('att_new_phone').value;
        }

        fetch('/atendimento', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error === 'already_open') {
                currentAttendanceId = data.id;
                isOpen = true;
                document.getElementById('phase2_client_name').textContent = data.client_name;
                document.getElementById('phase2_time_info').textContent   = 'Iniciado às ' + data.start_time;
                document.getElementById('att_modal_title').textContent    = 'Atendimento em andamento';
                showPhase(2);
                updateTopbarBtn(true);
                showToast('warning', 'Você já tem um atendimento em andamento.');
                return;
            }
            if (data.id) {
                currentAttendanceId = data.id;
                isOpen = true;
                document.getElementById('phase2_client_name').textContent = data.client_name;
                document.getElementById('phase2_time_info').textContent   =
                    'Iniciado às ' + data.start_time.slice(0, 5) + ' — ' + formatDate(data.date);
                document.getElementById('att_modal_title').textContent = 'Atendimento em andamento';
                showPhase(2);
                updateTopbarBtn(true);
                editor.focus();
            }
        })
        .catch(() => showToast('error', 'Erro ao iniciar atendimento.'));
    });

    // ── Encerrar atendimento ──────────────────────────────────────────────
    document.getElementById('btn_close_attendance').addEventListener('click', closeAttendance);

    function closeAttendance() {
        const notes = editor.innerHTML;
        document.getElementById('att_notes').value = notes;

        fetch(`/atendimento/${currentAttendanceId}/encerrar`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ notes }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const now = new Date();
                document.getElementById('phase3_summary').textContent =
                    'Encerrado às ' + now.toTimeString().slice(0, 5);
                document.getElementById('att_modal_title').textContent = 'Atendimento encerrado';
                showPhase(3);
                isOpen = false;
                updateTopbarBtn(false);
            }
        })
        .catch(() => showToast('error', 'Erro ao encerrar atendimento.'));
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

}); // DOMContentLoaded
</script>
