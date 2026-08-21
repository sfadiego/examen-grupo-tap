<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario' => ['required', 'email', 'max:255', 'unique:mongodb.usuario,usuario'],
            'nombre' => ['required', 'string', 'max:255'],
            'foto' => ['required', 'image', 'max:2048'],
            // Formato E.164: + seguido de 8 a 15 dígitos, sin espacios.
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^\+\d{8,15}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.required' => 'El usuario es requerido.',
            'usuario.email' => 'El usuario debe ser un correo electrónico válido.',
            'usuario.unique' => 'Ya existe un usuario con ese correo.',
            'nombre.required' => 'El nombre es requerido.',
            'foto.required' => 'La foto de perfil es requerida.',
            'foto.image' => 'La foto debe ser una imagen (jpg, png, etc).',
            'foto.max' => 'La foto no debe superar 2MB.',
            'telefono.regex' => 'El teléfono debe incluir el código de país, (ej: +523141234567).',
        ];
    }
}
