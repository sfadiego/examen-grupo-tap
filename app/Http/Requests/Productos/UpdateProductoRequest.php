<?php

namespace App\Http\Requests\Productos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('mongodb.producto', 'nombre')->ignore($this->route('producto'), '_id'),
            ],
            // Máximo 3 dígitos enteros, hasta 2 decimales opcionales.
            'precio' => ['sometimes', 'required', 'numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'marca' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es requerido.',
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
            'precio.required' => 'El precio es requerido.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.regex' => 'El precio debe tener máximo 3 dígitos enteros.',
            'marca.required' => 'La marca es requerida.',
        ];
    }
}
