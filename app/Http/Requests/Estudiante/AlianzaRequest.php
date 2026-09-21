<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class AlianzaRequest extends EjeRequest
{
    /**
     * La institución aliada se elige siempre del catálogo que administra DIGEU y no puede repetirse en un EPS.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'institucion_aliada_id' => [
                'required',
                'integer',
                Rule::exists('instituciones_aliadas', 'id')->whereNull('deleted_at'),
                Rule::unique('alianzas', 'institucion_aliada_id')
                    ->where('expediente_id', $this->route('expediente')?->getKey())
                    ->whereNull('deleted_at')
                    ->ignore($this->route('registro')),
            ],
            'aporte' => ['nullable', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'institucion_aliada_id.required' => 'Selecciona la institución aliada. Si no aparece en la lista, pide a DIGEU que la agregue.',
            'institucion_aliada_id.exists' => 'La institución elegida ya no está disponible. Selecciona otra de la lista.',
            'institucion_aliada_id.unique' => 'Esta institución ya está registrada como aliada de tu EPS.',
        ];
    }
}
