<?php

namespace App\Support;

use App\Enums\FuncionUniversitaria;
use App\Models\Expediente;

/**
 * Indicador de articulación de un EPS: cuántas de las cuatro funciones universitarias combina
 * (extensión, investigación, transferencia y vinculación), de 0 a 4.
 */
final readonly class Articulacion
{
    /**
     * @param  list<FuncionUniversitaria>  $funciones  Las que el EPS sí articula.
     */
    public function __construct(public array $funciones) {}

    /**
     * A partir de cuántos registros tiene el EPS de cada tipo.
     */
    public static function desdeConteos(int $bienesServicios, int $publicaciones, int $transferencias, int $alianzas): self
    {
        return new self(array_values(array_filter([
            $bienesServicios > 0 ? FuncionUniversitaria::Extension : null,
            $publicaciones > 0 ? FuncionUniversitaria::Investigacion : null,
            $transferencias > 0 ? FuncionUniversitaria::Transferencia : null,
            $alianzas > 0 ? FuncionUniversitaria::Vinculacion : null,
        ])));
    }

    /**
     * Usa los conteos ya cargados (withCount) y, si faltan, los consulta.
     */
    public static function deExpediente(Expediente $expediente): self
    {
        $conteo = fn (string $relacion, string $atributo): int => (int) ($expediente->getAttribute($atributo) ?? $expediente->{$relacion}()->count());

        return self::desdeConteos(
            $conteo('bienesServicios', 'bienes_servicios_count'),
            $conteo('publicaciones', 'publicaciones_count'),
            $conteo('transferencias', 'transferencias_count'),
            $conteo('alianzas', 'alianzas_count'),
        );
    }

    public function nivel(): int
    {
        return count($this->funciones);
    }

    public static function total(): int
    {
        return count(FuncionUniversitaria::cases());
    }

    public function etiqueta(): string
    {
        return match ($this->nivel()) {
            0 => 'Sin articulación',
            1 => 'Inicial',
            2 => 'Básica',
            3 => 'Articulada',
            default => 'Integral',
        };
    }

    public function porcentaje(): int
    {
        return (int) round($this->nivel() / self::total() * 100);
    }

    /**
     * @return array{nivel: int, total: int, porcentaje: int, etiqueta: string, funciones: list<array{clave: string, etiqueta: string, evidencia: string, presente: bool}>}
     */
    public function toArray(): array
    {
        return [
            'nivel' => $this->nivel(),
            'total' => self::total(),
            'porcentaje' => $this->porcentaje(),
            'etiqueta' => $this->etiqueta(),
            'funciones' => array_map(fn (FuncionUniversitaria $funcion): array => [
                'clave' => $funcion->value,
                'etiqueta' => $funcion->etiqueta(),
                'evidencia' => $funcion->evidencia(),
                'presente' => in_array($funcion, $this->funciones, true),
            ], FuncionUniversitaria::cases()),
        ];
    }
}
