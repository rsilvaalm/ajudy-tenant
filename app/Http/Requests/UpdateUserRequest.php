<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $this->route('id'),
            'profile_id' => 'required|integer|exists:profiles,id',
            'is_active'  => 'boolean',
            'is_lawyer'  => 'boolean',
            'oab'        => 'required_if:is_lawyer,1|nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'O nome é obrigatório.',
            'email.required'      => 'O e-mail é obrigatório.',
            'email.email'         => 'Informe um e-mail válido.',
            'email.unique'        => 'Este e-mail já está em uso.',
            'profile_id.required' => 'Selecione um perfil para o usuário.',
            'oab.required_if'     => 'Informe o número da OAB para advogados.',
        ];
    }
}
