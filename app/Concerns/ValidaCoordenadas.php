<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Reglas de una coordenada geográfica dentro del territorio de Guatemala (ambos valores o ninguno).
 */
trait ValidaCoordenadas
{
    /**
     * @return array<string, list<ValidationRule|string>>
     */
    protected function reglasCoordenadas(): array
    {
        return [
            'latitud' => ['nullable', 'numeric', 'between:13.5,18.5', 'required_with:longitud'],
            'longitud' => ['nullable', 'numeric', 'between:-92.5,-88', 'required_with:latitud'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function mensajesCoordenadas(): array
    {
        return [
            'latitud.between' => 'La latitud debe estar dentro del territorio de Guatemala (entre 13.5 y 18.5).',
            'longitud.between' => 'La longitud debe estar dentro del territorio de Guatemala (entre -92.5 y -88).',
            'latitud.required_with' => 'Ingresa la latitud junto con la longitud.',
            'longitud.required_with' => 'Ingresa la longitud junto con la latitud.',
        ];
    }
}
