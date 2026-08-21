<?php

namespace App\Http\Requests\Perfiles;

use Illuminate\Foundation\Http\FormRequest;

class AsignarSeccionesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seccion_ids' => ['required', 'array'],
            'seccion_ids.*' => ['exists:mongodb.seccion,_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'seccion_ids.required' => 'Debe indicar al menos una sección.',
            'seccion_ids.*.exists' => 'Una o más secciones indicadas no existen.',
        ];
    }
}
