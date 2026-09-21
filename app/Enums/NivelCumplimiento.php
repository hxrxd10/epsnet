<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum NivelCumplimiento: string
{
    use ConOpciones;

    case Cumplido = 'cumplido';
    case Parcial = 'parcial';
    case NoCumplido = 'no_cumplido';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Cumplido => 'Cumplido',
            self::Parcial => 'Cumplido parcialmente',
            self::NoCumplido => 'No cumplido',
        };
    }
}
