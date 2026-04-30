<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:255',
            'email'                => 'nullable|email|max:255',
            'profession'           => 'nullable|string|max:255',
            'birth_date'           => 'nullable|date',
            'father_name'          => 'nullable|string|max:255',
            'mother_name'          => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'mobile'               => 'nullable|string|max:20',
            'rg'                   => 'nullable|string|max:20',
            'cpf'                  => 'nullable|string|max:14|unique:clients,cpf',
            'marital_status'       => 'nullable|in:solteiro,casado,divorciado,viuvo,uniao_estavel,outro',
            'nationality'          => 'nullable|string|max:100',
            'address_street'       => 'nullable|string|max:255',
            'address_neighborhood' => 'nullable|string|max:100',
            'address_city'         => 'nullable|string|max:100',
            'address_state'        => 'nullable|string|max:2',
            'address_zip'          => 'nullable|string|max:9',
            'custom_fields'        => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cliente é obrigatório.',
            'cpf.unique'    => 'Este CPF já está cadastrado.',
            'email.email'   => 'Informe um e-mail válido.',
        ];
    }
}
