<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'module_ids'  => 'nullable|array',
            'module_ids.*'=> 'integer|exists:modules,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do perfil é obrigatório.',
        ];
    }
}
