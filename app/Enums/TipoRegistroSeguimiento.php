<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum TipoRegistroSeguimiento: string
{
    use ConOpciones;

    case Avance = 'avance';
    case EvaluacionFinal = 'evaluacion_final';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Avance => 'Avance parcial',
            self::EvaluacionFinal => 'Evaluación final de impacto',
        };
    }
}
