<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

/**
 * Lo que se puede consultar en el mapa de estadísticas; las claves son las de `Acumulador::metricas()`.
 */
enum MetricaEstadistica: string
{
    use ConOpciones;

    case Investigaciones = 'investigaciones';
    case BienesServicios = 'bienes_servicios';
    case Beneficiarios = 'beneficiarios';
    case Acciones = 'acciones';
    case Participantes = 'participantes';
    case Instituciones = 'instituciones';
    case Estudiantes = 'estudiantes';
    case Eps = 'eps';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Investigaciones => 'Investigaciones',
            self::BienesServicios => 'Bienes y servicios',
            self::Beneficiarios => 'Beneficiarios',
            self::Acciones => 'Acciones de transferencia',
            self::Participantes => 'Participantes',
            self::Instituciones => 'Instituciones aliadas',
            self::Estudiantes => 'Estudiantes',
            self::Eps => 'EPS',
        };
    }
}
