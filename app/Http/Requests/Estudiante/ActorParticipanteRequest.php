<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ActorParticipanteRequest extends EjeRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'institucion_receptora_id' => [
                'nullable',
                'integer',
                Rule::exists('instituciones_receptoras', 'id')->whereNull('deleted_at'),
            ],
            'institucion_nombre' => ['required_without:institucion_receptora_id', 'nullable', 'string', 'max:255'],
            'institucion_tipo' => ['nullable', 'string', 'max:100'],
            'institucion_nombre_contacto' => ['nullable', 'string', 'max:255'],
            'institucion_correo_contacto' => ['nullable', 'email', 'max:255'],
            'institucion_telefono_contacto' => ['nullable', 'string', 'max:30'],
            'contraparte' => ['required', 'string', 'max:255'],
            'comunidad_beneficiada' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'institucion_nombre.required_without' => 'Selecciona una institución receptora o registra una nueva.',
        ];
    }
}
