<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

/**
 * Catálogos que los administradores mantienen desde "Manejo de datos". Para agregar uno nuevo
 * basta con sumar un caso aquí; se guardan todos en la misma tabla `catalogos`.
 */
enum TipoCatalogo: string
{
    use ConOpciones;

    case BienesServicios = 'bienes-servicios';
    case Acciones = 'acciones';
    case LineasIntervencion = 'lineas-intervencion';
    case PoliticasPublicas = 'politicas-publicas';
    case AreasIntervencion = 'areas-intervencion';
    case Ods = 'ods';
    case PoblacionesVulnerables = 'poblaciones-vulnerables';

    public function etiqueta(): string
    {
        return match ($this) {
            self::BienesServicios => 'Bienes y servicios',
            self::Acciones => 'Acciones de transferencia de conocimiento',
            self::LineasIntervencion => 'Líneas de intervención',
            self::PoliticasPublicas => 'Políticas públicas',
            self::AreasIntervencion => 'Áreas de intervención',
            self::Ods => 'Objetivos de Desarrollo Sostenible (ODS)',
            self::PoblacionesVulnerables => 'Poblaciones vulnerables',
        };
    }

    public function descripcion(): string
    {
        return match ($this) {
            self::BienesServicios => 'Lo que los estudiantes generan para las comunidades durante su EPS (eje 1).',
            self::Acciones => 'Capacitaciones, talleres, asesorías y demás acciones de transferencia (eje 3).',
            self::LineasIntervencion => 'Líneas de intervención a las que se ancla cada bien o servicio (agua y saneamiento, seguridad alimentaria, etc.).',
            self::PoliticasPublicas => 'Políticas públicas asociadas a los bienes y servicios.',
            self::AreasIntervencion => 'Áreas de intervención de los bienes y servicios: salud, ambiente, educación, economía, gestión pública.',
            self::Ods => 'Objetivos de Desarrollo Sostenible a los que aporta cada bien o servicio.',
            self::PoblacionesVulnerables => 'Grupos en situación de vulnerabilidad que el estudiante marca entre los beneficiarios de cada bien o servicio.',
        };
    }

    /**
     * Si los elementos del catálogo se clasifican en categorías.
     *
     * @return list<array{value: string, label: string}>
     */
    public function categorias(): array
    {
        return match ($this) {
            self::BienesServicios => TipoBienServicio::opciones(),
            default => [],
        };
    }

    public function usaCategoria(): bool
    {
        return $this->categorias() !== [];
    }

    /**
     * Catálogos a los que se ancla cada elemento: campo de la tabla `catalogos` => catálogo de
     * donde se elige. Los bienes y servicios llevan su línea, política, área y ODS.
     *
     * @return array<string, self>
     */
    public function vinculos(): array
    {
        return match ($this) {
            self::BienesServicios => [
                'linea_intervencion_id' => self::LineasIntervencion,
                'politica_publica_id' => self::PoliticasPublicas,
                'area_intervencion_id' => self::AreasIntervencion,
                'ods_id' => self::Ods,
            ],
            default => [],
        };
    }

    /**
     * Nombre en singular del elemento de este catálogo, para las etiquetas de los formularios.
     */
    public function singular(): string
    {
        return match ($this) {
            self::BienesServicios => 'Bien o servicio',
            self::Acciones => 'Acción',
            self::LineasIntervencion => 'Línea de intervención',
            self::PoliticasPublicas => 'Política pública',
            self::AreasIntervencion => 'Área de intervención',
            self::Ods => 'ODS',
            self::PoblacionesVulnerables => 'Población vulnerable',
        };
    }

    /**
     * Si sus elementos llevan un valor base en quetzales (para calcular el aporte de los EPS).
     */
    public function usaValorBase(): bool
    {
        return $this === self::BienesServicios;
    }
}
