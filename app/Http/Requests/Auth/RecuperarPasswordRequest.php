<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RecuperarPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario' => ['required', 'email', 'exists:mongodb.usuario,usuario'],
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.required' => 'El usuario es requerido.',
            'usuario.email' => 'El usuario debe ser un correo electrónico válido.',
            'usuario.exists' => 'No existe ningún usuario con ese correo.',
        ];
    }
}
