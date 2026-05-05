{{-- ── Modal: Novo Processo ─────────────────────────────────────────────── --}}
<div class="modal fade" id="newProcessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--brand-primary);">
                <h5 class="modal-title text-white">
                    <i class="ri-scales-line me-2"></i> Novo Processo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="proc_form_error" class="alert alert-danger py-2 mb-3 d-none fs-13"></div>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Pasta</label>
                        <select id="proc_folder_id" class="form-select">
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="text" id="proc_date" class="form-control flatpickr-proc"
                               placeholder="DD/MM/AAAA" autocomplete="off">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Número do processo <span class="text-danger">*</span></label>
                        <input type="text" id="proc_number" class="form-control"
                               placeholder="Ex: 1004017-79.2023.4.01.3302">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Polo ativo <span class="text-danger">*</span></label>
                        <input type="text" id="proc_active_pole" class="form-control proc-uppercase"
                               placeholder="POLO ATIVO">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Polo passivo <span class="text-danger">*</span></label>
                        <input type="text" id="proc_passive_pole" class="form-control proc-uppercase"
                               placeholder="POLO PASSIVO">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Local do processo</label>
                        <input type="text" id="proc_location" class="form-control proc-uppercase"
                               placeholder="EX: 1ª VARA DO TRABALHO">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_save_process">
                    <i class="ri-save-line me-1"></i> Salvar processo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Carrega Flatpickr no modal (lazy)
    function initProcDatepicker() {
        const el = document.getElementById('proc_date');
        if (el && !el._flatpickr) {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(el, {
                    locale: 'pt', dateFormat: 'Y-m-d',
                    altInput: true, altFormat: 'd/m/Y', allowInput: true,
                });
            } else {
                // Flatpickr ainda não carregado — tenta após carregar scripts
                const s1 = document.createElement('link');
                s1.rel = 'stylesheet';
                s1.href = 'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css';
                document.head.appendChild(s1);

                const s2 = document.createElement('script');
                s2.src = 'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js';
                s2.onload = function () {
                    const s3 = document.createElement('script');
                    s3.src = 'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/pt.js';
                    s3.onload = function () {
                        flatpickr(el, {
                            locale: 'pt', dateFormat: 'Y-m-d',
                            altInput: true, altFormat: 'd/m/Y', allowInput: true,
                        });
                    };
                    document.head.appendChild(s3);
                };
                document.head.appendChild(s2);
            }
        }
    }

    // Maiúsculas nos campos do modal
    document.querySelectorAll('.proc-uppercase').forEach(el => {
        el.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    });

    // Carrega pastas no select
    function loadFolders() {
        fetch('/processos/pastas-json', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(folders => {
            const sel = document.getElementById('proc_folder_id');
            folders.forEach(f => {
                if (![...sel.options].some(o => o.value == f.id)) {
                    const opt = document.createElement('option');
                    opt.value = f.id; opt.textContent = f.name;
                    sel.appendChild(opt);
                }
            });
        }).catch(() => {});
    }

    document.getElementById('newProcessModal').addEventListener('show.bs.modal', function () {
        if (document.getElementById('proc_folder_id').options.length <= 1) loadFolders();
        initProcDatepicker();

        if (!document.getElementById('proc_date').value) {
            // Seta data de hoje no input hidden (o flatpickr usa o altInput para exibir)
            const today = new Date().toISOString().split('T')[0];
            const fp = document.getElementById('proc_date')._flatpickr;
            if (fp) fp.setDate(today);
            else document.getElementById('proc_date').value = today;
        }

        const clientName = this.dataset.clientName || '';
        if (!document.getElementById('proc_active_pole').value)
            document.getElementById('proc_active_pole').value = clientName;
    });

    document.getElementById('btn_save_process').addEventListener('click', function () {
        const clientId    = document.getElementById('newProcessModal').dataset.clientId;
        const number      = document.getElementById('proc_number').value.trim();
        const folderId    = document.getElementById('proc_folder_id').value;
        const location    = document.getElementById('proc_location').value;
        const activePole  = document.getElementById('proc_active_pole').value.trim();
        const passivePole = document.getElementById('proc_passive_pole').value.trim();
        const errEl       = document.getElementById('proc_form_error');

        // Pega data do flatpickr (input hidden) ou do campo diretamente
        const fp = document.getElementById('proc_date')._flatpickr;
        const date = fp ? fp.selectedDates[0]
            ? fp.formatDate(fp.selectedDates[0], 'Y-m-d') : ''
            : document.getElementById('proc_date').value;

        errEl.classList.add('d-none');

        if (!number || !date || !activePole || !passivePole) {
            errEl.textContent = 'Preencha todos os campos obrigatórios.';
            errEl.classList.remove('d-none');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

        fetch('/processos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                client_id: clientId, folder_id: folderId || null,
                number, date, active_pole: activePole,
                passive_pole: passivePole, location,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('newProcessModal')).hide();
                showToast('success', 'Processo cadastrado com sucesso!');
                // Limpa campos
                ['proc_number','proc_active_pole','proc_passive_pole','proc_location'].forEach(id => {
                    document.getElementById(id).value = '';
                });
                document.getElementById('proc_folder_id').value = '';
                const fp = document.getElementById('proc_date')._flatpickr;
                if (fp) fp.clear();
                // Recarrega lista
                if (typeof loadClientProcesses === 'function') loadClientProcesses();
            } else if (data.errors) {
                errEl.textContent = Object.values(data.errors)[0][0];
                errEl.classList.remove('d-none');
            } else {
                errEl.textContent = data.message || 'Erro ao salvar.';
                errEl.classList.remove('d-none');
            }
        })
        .catch(() => {
            errEl.textContent = 'Erro ao salvar processo.';
            errEl.classList.remove('d-none');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="ri-save-line me-1"></i> Salvar processo';
        });
    });
});
</script>
