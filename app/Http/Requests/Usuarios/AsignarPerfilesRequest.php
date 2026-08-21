<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class AsignarPerfilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perfil_ids' => ['required', 'array'],
            'perfil_ids.*' => ['exists:mongodb.perfil,_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'perfil_ids.required' => 'Debe indicar al menos un perfil.',
            'perfil_ids.*.exists' => 'Uno o más perfiles indicados no existen.',
        ];
    }
}
