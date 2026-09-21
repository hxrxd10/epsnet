<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum TipoCambioBitacora: string
{
    use ConOpciones;

    case Creacion = 'creacion';
    case Edicion = 'edicion';
    case Eliminacion = 'eliminacion';
    case Aprobacion = 'aprobacion';
    case RetiroAprobacion = 'retiro_aprobacion';
    case Acceso = 'acceso';
    case Exportacion = 'exportacion';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Creacion => 'Creó',
            self::Edicion => 'Editó',
            self::Eliminacion => 'Eliminó',
            self::Aprobacion => 'Aprobó',
            self::RetiroAprobacion => 'Retiró aprobación',
            self::Acceso => 'Acceso',
            self::Exportacion => 'Exportó',
        };
    }
}
