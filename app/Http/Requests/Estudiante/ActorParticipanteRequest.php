<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Contracts\Validation\ValidationRule;

class ActorParticipanteRequest extends EjeRequest
{
    /**
     * La institución receptora es donde el estudiante realiza su EPS y la escribe él mismo.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'institucion_receptora' => ['required', 'string', 'max:255'],
            'contraparte' => ['required', 'string', 'max:255'],
            'comunidad_beneficiada' => ['required', 'string', 'max:255'],
        ];
    }
}
