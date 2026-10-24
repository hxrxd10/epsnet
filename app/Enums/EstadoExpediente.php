<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum EstadoExpediente: string
{
    use ConOpciones;

    /** El estudiante todavía está llenando su información. */
    case Activo = 'activo';

    /** El estudiante envió su EPS a aprobación: espera en la bandeja de supervisión. */
    case Completo = 'completo';

    /** La supervisión de la unidad académica aprobó el EPS: cuenta para las estadísticas. */
    case Verificado = 'verificado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Activo => 'En progreso',
            self::Completo => 'En revisión',
            self::Verificado => 'Aprobado',
        };
    }

    /**
     * Estados cuya información es válida para las estadísticas: solo los EPS aprobados por supervisión.
     *
     * @return list<self>
     */
    public static function validos(): array
    {
        return [self::Verificado];
    }
}
