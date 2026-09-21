<?php

namespace App\Enums;

enum Eje: int
{
    case BienesServicios = 1;
    case Publicaciones = 2;
    case Transferencia = 3;
    case Territorio = 4;
    case Actores = 5;
    case Seguimiento = 6;

    public function titulo(): string
    {
        return match ($this) {
            self::BienesServicios => 'Bienes y servicios generados',
            self::Publicaciones => 'Publicaciones de investigación',
            self::Transferencia => 'Transferencia de conocimiento',
            self::Territorio => 'Territorio y geolocalización',
            self::Actores => 'Actores y participantes',
            self::Seguimiento => 'Seguimiento e impacto',
        };
    }

    /**
     * Segmento de URL de la sección del eje.
     */
    public function segmento(): string
    {
        return match ($this) {
            self::BienesServicios => 'bienes-servicios',
            self::Publicaciones => 'publicaciones',
            self::Transferencia => 'transferencias',
            self::Territorio => 'territorio',
            self::Actores => 'actores',
            self::Seguimiento => 'seguimiento',
        };
    }

    /**
     * Nombre de la relación del expediente que contiene los registros del eje.
     */
    public function relacion(): string
    {
        return match ($this) {
            self::BienesServicios => 'bienesServicios',
            self::Publicaciones => 'publicaciones',
            self::Transferencia => 'transferencias',
            self::Territorio => 'ubicaciones',
            self::Actores => 'actores',
            self::Seguimiento => 'seguimientos',
        };
    }

    /**
     * Nombre de ruta del listado del eje.
     */
    public function ruta(): string
    {
        return 'estudiante.expedientes.'.$this->segmento().'.index';
    }
}
