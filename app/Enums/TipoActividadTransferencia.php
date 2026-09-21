<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum TipoActividadTransferencia: string
{
    use ConOpciones;

    case Capacitacion = 'capacitacion';
    case Taller = 'taller';
    case Asesoria = 'asesoria';
    case Documento = 'documento';
    case Otro = 'otro';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Capacitacion => 'Capacitación',
            self::Taller => 'Taller',
            self::Asesoria => 'Asesoría',
            self::Documento => 'Documento o material generado',
            self::Otro => 'Otra actividad',
        };
    }
}
