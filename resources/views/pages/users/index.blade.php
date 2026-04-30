@extends('layouts.app')
@section('title', 'Usuários')
@section('page-title', 'Usuários')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Usuários cadastrados</h5>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('usuarios.index') }}" class="d-flex gap-2">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control form-control-sm" placeholder="Buscar usuário..."
                       style="width:220px;">
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="ri-search-line"></i>
                </button>
            </form>
            <a href="{{ route('usuarios.create') }}" class="btn btn-sm btn-primary">
                <i class="ri-add-line me-1"></i> Novo Usuário
            </a>
        </div>
    </div>
    <div class="card-body p-0" style="min-height:200px;">
        <div class="table-responsive" style="overflow:visible;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th class="text-end" style="width:120px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar-xs rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold fs-12"
                                      style="width:32px;height:32px;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <span class="fw-medium">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            @if($user->profile_name)
                                <span class="badge bg-info-subtle text-info">{{ $user->profile_name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success">Ativo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inativo</span>
                            @endif
                        </td>
                        <td class="text-muted">
                            {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('usuarios.edit', $user->id) }}"
                               class="btn btn-sm btn-soft-primary me-1">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('usuarios.toggle', $user->id) }}"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm {{ $user->is_active ? 'btn-soft-warning' : 'btn-soft-success' }}"
                                        title="{{ $user->is_active ? 'Desativar' : 'Ativar' }}">
                                    <i class="ri-{{ $user->is_active ? 'pause' : 'play' }}-circle-line"></i>
                                </button>
                            </form>
                            @if($user->id !== auth()->id())
                            <button onclick="confirmDelete(
                                        '{{ route('usuarios.destroy', $user->id) }}',
                                        'Remover o usuário {{ addslashes($user->name) }}?')"
                                    class="btn btn-sm btn-soft-danger ms-1">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="ri-user-line fs-36 d-block mb-2 opacity-25"></i>
                            Nenhum usuário cadastrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 pb-3 pt-2">
            {{ $users->links() }}
        </div>
    </div>
</div>

@endsection
