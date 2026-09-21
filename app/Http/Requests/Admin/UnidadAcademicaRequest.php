<?php

namespace App\Http\Requests\Admin;

use App\Concerns\ValidaCoordenadas;
use App\Enums\TipoUnidadAcademica;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnidadAcademicaRequest extends FormRequest
{
    use ValidaCoordenadas;

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
            'nombre' => ['required', 'string', 'max:255', Rule::unique('unidades_academicas', 'nombre')->ignore($this->route('unidad'))],
            'siglas' => ['nullable', 'string', 'max:30'],
            'tipo' => ['required', Rule::enum(TipoUnidadAcademica::class)],
            'nombre_contacto' => ['nullable', 'string', 'max:255'],
            'correo_contacto' => ['nullable', 'email', 'max:255'],
            'telefono_contacto' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'activa' => ['required', 'boolean'],
            ...$this->reglasCoordenadas(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe una unidad académica con este nombre.',
            ...$this->mensajesCoordenadas(),
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
