<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum ProgramaEps: string
{
    use ConOpciones;

    case EpsFacultativo = 'eps_facultativo';
    case Epsum = 'epsum';

    public function etiqueta(): string
    {
        return match ($this) {
            self::EpsFacultativo => 'EPS facultativo',
            self::Epsum => 'EPSUM',
        };
    }
}
