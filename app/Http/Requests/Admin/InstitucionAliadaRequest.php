<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstitucionAliadaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('instituciones_aliadas', 'nombre')->ignore($this->route('institucion'))],
            'tipo' => ['nullable', 'string', 'max:100'],
            'nombre_contacto' => ['nullable', 'string', 'max:255'],
            'correo_contacto' => ['nullable', 'email', 'max:255'],
            'telefono_contacto' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe una institución aliada con este nombre.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'el nombre',
            'tipo' => 'el tipo',
            'nombre_contacto' => 'el nombre de contacto',
            'correo_contacto' => 'el correo de contacto',
            'telefono_contacto' => 'el teléfono de contacto',
            'direccion' => 'la dirección',
        ];
    }
}
