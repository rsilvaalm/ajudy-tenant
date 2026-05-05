@extends('layouts.app')
@section('title', 'Atendimentos')
@section('page-title', 'Atendimentos')

@section('content')

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('atendimentos.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" value="{{ $search }}"
                           class="form-control" placeholder="Cliente ou atendente...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">De</label>
                    <input type="date" name="from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Até</label>
                    <input type="date" name="to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-search-line me-1"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Listagem --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
            {{ $attendances->total() }} atendimento(s) encontrado(s)
        </h5>
        <small class="text-muted">
            {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
            até
            {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
        </small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Duração</th>
                        <th>Atendente</th>
                        <th>Status</th>
                        <th class="text-end" style="width:80px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr style="cursor:pointer;" onclick="openAttendanceModal({{ $att->id }})">
                        <td class="fw-medium">{{ $att->client_name }}</td>
                        <td class="text-muted">
                            {{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}
                        </td>
                        <td class="text-muted">{{ substr($att->start_time, 0, 5) }}</td>
                        <td class="text-muted">{{ $att->end_time ? substr($att->end_time, 0, 5) : '—' }}</td>
                        <td class="text-muted">
                            @if($att->start_time && $att->end_time)
                                @php
                                    $start = \Carbon\Carbon::parse($att->date . ' ' . $att->start_time);
                                    $end   = \Carbon\Carbon::parse($att->date . ' ' . $att->end_time);
                                    $diff  = $start->diffInMinutes($end);
                                    echo $diff >= 60
                                        ? floor($diff/60).'h '.($diff%60 > 0 ? ($diff%60).'min' : '')
                                        : $diff.'min';
                                @endphp
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-muted">{{ $att->user_name }}</td>
                        <td>
                            @if($att->status === 'open')
                                <span class="badge bg-warning-subtle text-warning">Em andamento</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Encerrado</span>
                            @endif
                        </td>
                        <td class="text-end" onclick="event.stopPropagation()">
                            @if($att->user_id === auth()->id())
                            <button onclick="deleteAttendance({{ $att->id }})"
                                    class="btn btn-sm btn-soft-danger"
                                    title="Remover">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="ri-customer-service-2-line fs-36 d-block mb-2 opacity-25"></i>
                            Nenhum atendimento no período selecionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="px-3 pb-3 pt-2">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal de visualização do atendimento --}}
<div class="modal fade" id="viewAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--brand-primary);">
                <h5 class="modal-title text-white">
                    <i class="ri-customer-service-2-line me-2"></i>
                    <span id="view_att_title">Atendimento</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="view_att_body">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const authUserId = {{ auth()->id() }};

    function openAttendanceModal(id) {
        const modal = new bootstrap.Modal(document.getElementById('viewAttendanceModal'));
        document.getElementById('view_att_body').innerHTML =
            '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
        modal.show();

        fetch(`/atendimentos/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(att => {
            document.getElementById('view_att_title').textContent = att.client_name;

            const start = att.start_time ? att.start_time.slice(0,5) : '—';
            const end   = att.end_time   ? att.end_time.slice(0,5)   : '—';
            const date  = formatDate(att.date);

            const statusBadge = att.status === 'open'
                ? '<span class="badge bg-warning-subtle text-warning">Em andamento</span>'
                : '<span class="badge bg-success-subtle text-success">Encerrado</span>';

            const canEdit = att.user_id === authUserId;

            document.getElementById('view_att_body').innerHTML = `
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted fs-12 mb-1">Data</label>
                        <p class="fw-medium mb-0">${date}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fs-12 mb-1">Início</label>
                        <p class="fw-medium mb-0">${start}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fs-12 mb-1">Fim</label>
                        <p class="fw-medium mb-0">${end}</p>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted fs-12 mb-1">Status</label>
                        <p class="mb-0">${statusBadge}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fs-12 mb-1">Atendente</label>
                        <p class="fw-medium mb-0">${att.user_name}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fs-12 mb-1">E-mail / Telefone do cliente</label>
                        <p class="fw-medium mb-0 text-muted">${[att.client_email, att.client_phone].filter(Boolean).join(' · ') || '—'}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted fs-12 mb-1">Anotações</label>
                    <div class="p-3 rounded border" style="min-height:100px;line-height:1.6;font-size:14px;">
                        ${att.notes || '<span class="text-muted">Sem anotações registradas.</span>'}
                    </div>
                </div>

                ${canEdit ? `
                <div class="mt-3 text-end">
                    <button onclick="deleteAttendance(${att.id}, true)"
                            class="btn btn-sm btn-soft-danger">
                        <i class="ri-delete-bin-line me-1"></i> Remover atendimento
                    </button>
                </div>` : ''}
            `;
        });
    }

    function deleteAttendance(id, fromModal = false) {
        if (!confirm('Remover este atendimento?')) return;

        fetch(`/atendimentos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Atendimento removido!');
                if (fromModal) {
                    bootstrap.Modal.getInstance(document.getElementById('viewAttendanceModal')).hide();
                }
                setTimeout(() => location.reload(), 800);
            } else if (data.error === 'forbidden') {
                showToast('error', 'Você não tem permissão para remover este atendimento.');
            }
        });
    }

    function formatDate(d) {
        if (!d) return '—';
        const [y, m, day] = d.split('-');
        return `${day}/${m}/${y}`;
    }
</script>
@endpush

@endsection
