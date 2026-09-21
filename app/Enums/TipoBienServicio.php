<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum TipoBienServicio: string
{
    use ConOpciones;

    case Bien = 'bien';
    case Servicio = 'servicio';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Bien => 'Bien',
            self::Servicio => 'Servicio',
        };
    }
}
