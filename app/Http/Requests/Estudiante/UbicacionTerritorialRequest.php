<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UbicacionTerritorialRequest extends EjeRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'municipio_id' => [
                'required',
                'integer',
                Rule::exists('municipios', 'id')->where('departamento_id', $this->input('departamento_id')),
            ],
            'comunidad' => ['nullable', 'string', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:13.5,18.5', 'required_with:longitud'],
            'longitud' => ['nullable', 'numeric', 'between:-92.5,-88', 'required_with:latitud'],
            'referencia' => ['nullable', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'municipio_id.exists' => 'El municipio no pertenece al departamento elegido.',
            'latitud.between' => 'La latitud debe estar dentro del territorio de Guatemala (entre 13.5 y 18.5).',
            'longitud.between' => 'La longitud debe estar dentro del territorio de Guatemala (entre -92.5 y -88).',
            'latitud.required_with' => 'Ingresa la latitud junto con la longitud.',
            'longitud.required_with' => 'Ingresa la longitud junto con la latitud.',
        ];
    }
}
