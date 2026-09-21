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

    public function etiqueta(): string
    {
        return match ($this) {
            self::BienesServicios => 'Bienes y servicios',
            self::Acciones => 'Acciones de transferencia de conocimiento',
        };
    }

    public function descripcion(): string
    {
        return match ($this) {
            self::BienesServicios => 'Lo que los estudiantes generan para las comunidades durante su EPS (eje 1).',
            self::Acciones => 'Capacitaciones, talleres, asesorías y demás acciones de transferencia (eje 3).',
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
            self::Acciones => [],
        };
    }

    public function usaCategoria(): bool
    {
        return $this->categorias() !== [];
    }
}
