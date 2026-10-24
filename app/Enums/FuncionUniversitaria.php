<?php

namespace App\Enums;

/**
 * Funciones universitarias que un EPS puede articular (además de la docencia, que es el EPS mismo).
 * Con ellas se calcula el indicador de articulación.
 */
enum FuncionUniversitaria: string
{
    case Extension = 'extension';
    case Investigacion = 'investigacion';
    case Transferencia = 'transferencia';
    case Vinculacion = 'vinculacion';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Extension => 'Extensión',
            self::Investigacion => 'Investigación',
            self::Transferencia => 'Transferencia de conocimiento',
            self::Vinculacion => 'Vinculación con aliadas',
        };
    }

    /**
     * Qué registros del EPS demuestran que articuló esta función.
     */
    public function evidencia(): string
    {
        return match ($this) {
            self::Extension => 'Bienes y servicios entregados a la comunidad',
            self::Investigacion => 'Publicaciones de investigación',
            self::Transferencia => 'Acciones de transferencia de conocimiento',
            self::Vinculacion => 'Instituciones aliadas que cooperaron',
        };
    }
}
