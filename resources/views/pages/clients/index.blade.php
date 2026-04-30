@extends('layouts.app')
@section('title', 'Clientes')
@section('page-title', 'Pesquisar Cliente')

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('clientes.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Nome do cliente</label>
                    <input type="text" name="q" value="{{ $search }}"
                           class="form-control" placeholder="Digite o nome..."
                           autofocus>
                </div>
                <div class="col-md-3">
                    <label class="form-label">RG</label>
                    <input type="text" name="rg" value="{{ $rg }}"
                           class="form-control" placeholder="RG">
                </div>
                <div class="col-md-3">
                    <label class="form-label">CPF</label>
                    <input type="text" name="cpf" value="{{ $cpf }}"
                           class="form-control" placeholder="000.000.000-00">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($clients !== null)

    @if($clients->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ri-user-search-line fs-36 d-block mb-2 opacity-25"></i>
        Nenhum cliente encontrado.
    </div>
    @else
    <div class="row g-3">
        @foreach($clients as $client)
        <div class="col-md-4">
            <div class="card h-100 border">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $client->name }}</h6>
                    <div class="text-muted fs-13">
                        <div><span class="fw-medium">CPF:</span> {{ $client->cpf ?? 'Não informado' }}</div>
                        <div><span class="fw-medium">RG:</span> {{ $client->rg ?? 'Não informado' }}</div>
                        <div><span class="fw-medium">Telefone:</span> {{ $client->phone ?? 'Não informado' }}</div>
                        <div><span class="fw-medium">Celular:</span> {{ $client->mobile ?? 'Não informado' }}</div>
                        <div><span class="fw-medium">E-mail:</span> {{ $client->email ?? 'Não informado' }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('clientes.show', $client->id) }}"
                       class="btn btn-primary w-100">
                        <i class="ri-arrow-right-circle-line me-1"></i> Acessar cliente
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

@endif

@endsection
