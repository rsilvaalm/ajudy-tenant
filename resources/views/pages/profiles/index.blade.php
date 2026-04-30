@extends('layouts.app')
@section('title', 'Perfis de Usuário')
@section('page-title', 'Perfis de Usuário')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Perfis cadastrados</h5>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('perfis.index') }}" class="d-flex gap-2">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control form-control-sm" placeholder="Buscar perfil..."
                       style="width:220px;">
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="ri-search-line"></i>
                </button>
            </form>
            <a href="{{ route('perfis.create') }}" class="btn btn-sm btn-primary">
                <i class="ri-add-line me-1"></i> Novo Perfil
            </a>
        </div>
    </div>
    <div class="card-body p-0" style="min-height:200px;">
        <div class="table-responsive" style="overflow:visible;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Módulos</th>
                        <th>Tipo</th>
                        <th class="text-end" style="width:100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                    <tr>
                        <td class="fw-medium">{{ $profile->name }}</td>
                        <td class="text-muted">{{ $profile->description ?? '—' }}</td>
                        <td>
                            @php
                                $count = \Illuminate\Support\Facades\DB::table('profile_module')
                                    ->where('profile_id', $profile->id)->count();
                            @endphp
                            <span class="badge bg-info-subtle text-info">{{ $count }} módulo(s)</span>
                        </td>
                        <td>
                            @if($profile->is_system)
                                <span class="badge bg-warning-subtle text-warning">Sistema</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Personalizado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if(!$profile->is_system)
                            <a href="{{ route('perfis.edit', $profile->id) }}"
                               class="btn btn-sm btn-soft-primary me-1">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <button onclick="confirmDelete(
                                        '{{ route('perfis.destroy', $profile->id) }}',
                                        'Remover o perfil {{ addslashes($profile->name) }}?')"
                                    class="btn btn-sm btn-soft-danger">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            @else
                            <span class="text-muted fs-12">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="ri-shield-user-line fs-36 d-block mb-2 opacity-25"></i>
                            Nenhum perfil cadastrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 pb-3 pt-2">
            {{ $profiles->links() }}
        </div>
    </div>
</div>

@endsection
