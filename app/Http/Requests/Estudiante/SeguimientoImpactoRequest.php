<?php

namespace App\Http\Requests\Estudiante;

use App\Enums\NivelCumplimiento;
use App\Enums\TipoRegistroSeguimiento;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class SeguimientoImpactoRequest extends EjeRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo_registro' => ['required', Rule::enum(TipoRegistroSeguimiento::class)],
            'indicador' => ['required', 'string', 'max:500'],
            'avance' => ['nullable', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'porcentaje_avance' => ['nullable', 'integer', 'between:0,100'],
            'cumplimiento' => ['nullable', Rule::enum(NivelCumplimiento::class)],
            'observaciones' => ['nullable', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'evaluacion_impacto' => [
                Rule::requiredIf(fn (): bool => $this->input('tipo_registro') === TipoRegistroSeguimiento::EvaluacionFinal->value),
                'nullable',
                'string',
                'max:'.self::LIMITE_TEXTO_LARGO,
            ],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
