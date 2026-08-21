<?php

namespace App\Http\Requests\Productos;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255',  'unique:mongodb.producto,nombre'],
            // Máximo 3 dígitos enteros, hasta 2 decimales opcionales.
            'precio' => ['required', 'numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'marca' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es requerido.',
            'precio.required' => 'El precio es requerido.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.regex' => 'El precio debe tener máximo 3 dígitos enteros.',
            'marca.required' => 'La marca es requerida.',
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
        ];
    }
}
