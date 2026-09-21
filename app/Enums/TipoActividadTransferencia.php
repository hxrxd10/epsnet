<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum TipoActividadTransferencia: string
{
    use ConOpciones;

    case Capacitacion = 'capacitacion';
    case Taller = 'taller';
    case Asesoria = 'asesoria';
    case Otro = 'otro';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Capacitacion => 'Capacitación',
            self::Taller => 'Taller',
            self::Asesoria => 'Asesoría',
            self::Otro => 'Otra actividad',
        };
    }
}
