@extends('layouts.app')
@section('title', 'Editar Cliente')
@section('page-title', $client->name)

@section('content')

<form method="POST" action="{{ route('clientes.update', $client->id) }}">
@csrf
@method('PUT')

{{-- ── Dados Pessoais ──────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Dados Pessoais</h5>
        <a href="{{ route('clientes.show', $client->id) }}" class="btn btn-sm btn-light">
            <i class="ri-arrow-left-line me-1"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $client->name) }}"
                       class="form-control uppercase @error('name') is-invalid @enderror">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}"
                       class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Profissão</label>
                <input type="text" name="profession" value="{{ old('profession', $client->profession) }}"
                       class="form-control uppercase">
            </div>
            <div class="col-md-2">
                <label class="form-label">Data de nascimento</label>
                <input type="text" name="birth_date"
                       value="{{ old('birth_date', $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('Y-m-d') : '') }}"
                       class="form-control flatpickr-date"
                       placeholder="DD/MM/AAAA" autocomplete="off">
            </div>
            <div class="col-md-5">
                <label class="form-label">Nome do pai</label>
                <input type="text" name="father_name" value="{{ old('father_name', $client->father_name) }}"
                       class="form-control uppercase">
            </div>
            <div class="col-md-5">
                <label class="form-label">Nome da mãe</label>
                <input type="text" name="mother_name" value="{{ old('mother_name', $client->mother_name) }}"
                       class="form-control uppercase">
            </div>
            <div class="col-md-2">
                <label class="form-label">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                       class="form-control mask-phone">
            </div>
            <div class="col-md-2">
                <label class="form-label">Celular</label>
                <input type="text" name="mobile" value="{{ old('mobile', $client->mobile) }}"
                       class="form-control mask-mobile">
            </div>
            <div class="col-md-2">
                <label class="form-label">RG</label>
                <input type="text" name="rg" value="{{ old('rg', $client->rg) }}"
                       class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" value="{{ old('cpf', $client->cpf) }}"
                       class="form-control mask-cpf @error('cpf') is-invalid @enderror">
                @error('cpf')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado civil</label>
                <select name="marital_status" class="form-select">
                    <option value="">Selecione...</option>
                    @foreach(['solteiro'=>'Solteiro(a)','casado'=>'Casado(a)','divorciado'=>'Divorciado(a)','viuvo'=>'Viúvo(a)','uniao_estavel'=>'União estável','outro'=>'Outro'] as $val => $label)
                        <option value="{{ $val }}" {{ old('marital_status', $client->marital_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nacionalidade</label>
                <input type="text" name="nationality" value="{{ old('nationality', $client->nationality) }}"
                       class="form-control uppercase">
            </div>
        </div>
    </div>
</div>

{{-- ── Endereço ────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Endereço</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Linha 1: Rua e Bairro --}}
            <div class="col-md-8">
                <label class="form-label">Rua / Logradouro</label>
                <input type="text" name="address_street"
                       value="{{ old('address_street', $client->address_street) }}"
                       class="form-control uppercase">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bairro</label>
                <input type="text" name="address_neighborhood"
                       value="{{ old('address_neighborhood', $client->address_neighborhood) }}"
                       class="form-control uppercase">
            </div>
            {{-- Linha 2: Cidade, UF, CEP --}}
            <div class="col-md-5">
                <label class="form-label">Cidade</label>
                <input type="text" name="address_city"
                       value="{{ old('address_city', $client->address_city) }}"
                       class="form-control uppercase">
            </div>
            <div class="col-md-2">
                <label class="form-label">UF</label>
                <input type="text" name="address_state"
                       value="{{ old('address_state', $client->address_state) }}"
                       class="form-control uppercase" maxlength="2">
            </div>
            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" name="address_zip"
                       value="{{ old('address_zip', $client->address_zip) }}"
                       class="form-control mask-cep">
            </div>
        </div>
    </div>
</div>

{{-- ── Informações Adicionais ──────────────────────────────────────────── --}}
@if($customFields->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Informações Adicionais</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($customFields as $field)
            <div class="col-md-4">
                <label class="form-label">
                    {{ $field->label }}
                    @if($field->required)<span class="text-danger">*</span>@endif
                </label>
                <input type="text"
                       name="custom_fields[{{ $field->id }}]"
                       value="{{ old("custom_fields.{$field->id}", $field->value) }}"
                       class="form-control"
                       {{ $field->required ? 'required' : '' }}>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="ri-save-line me-1"></i> Salvar alterações
    </button>
    <a href="{{ route('clientes.show', $client->id) }}" class="btn btn-light">Cancelar</a>
</div>

</form>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/pt.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.6.1/imask.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.uppercase').forEach(el => {
        el.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    });

    flatpickr('.flatpickr-date', {
        locale: 'pt',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        maxDate: 'today',
    });

    document.querySelectorAll('.mask-cpf').forEach(el => {
        IMask(el, { mask: '000.000.000-00' });
    });
    document.querySelectorAll('.mask-phone').forEach(el => {
        IMask(el, { mask: '(00) 0000-0000' });
    });
    document.querySelectorAll('.mask-mobile').forEach(el => {
        IMask(el, { mask: '(00) 00000-0000' });
    });
    document.querySelectorAll('.mask-cep').forEach(el => {
        IMask(el, { mask: '00000-000' });
    });

});
</script>
@endpush

@endsection
