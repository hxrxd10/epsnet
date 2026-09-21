<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum EstadoExpediente: string
{
    use ConOpciones;

    /** El estudiante todavía está llenando su información. */
    case Activo = 'activo';

    /** El estudiante revisó el resumen y subió su orden de impresión: cuenta para las estadísticas. */
    case Completo = 'completo';

    /** La unidad académica aprobó el expediente (doble verificación). */
    case Verificado = 'verificado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Activo => 'En progreso',
            self::Completo => 'Completo',
            self::Verificado => 'Verificado',
        };
    }

    /**
     * Estados cuya información es válida para las estadísticas.
     *
     * @return list<self>
     */
    public static function validos(): array
    {
        return [self::Completo, self::Verificado];
    }
}
