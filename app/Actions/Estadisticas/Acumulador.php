<?php

namespace App\Actions\Estadisticas;

/**
 * Suma los registros de varios expedientes en las métricas que muestran las estadísticas.
 *
 * @phpstan-type Registro array{estudiante: int, bienes: int, aporte: float, beneficiarios: int, acciones: int, participantes: int, investigaciones: int, instituciones: list<int>, items: array<string, array{nombre: string, tipo: string, cantidad: int, beneficiarios: int, aporte: float}>}
 */
class Acumulador
{
    private int $expedientes = 0;

    private int $bienes = 0;

    /** Quetzales: cantidad de cada bien o servicio por su valor base. */
    private float $aporte = 0;

    private int $beneficiarios = 0;

    private int $acciones = 0;

    private int $participantes = 0;

    private int $investigaciones = 0;

    /** @var array<int, true> */
    private array $estudiantes = [];

    /** @var array<int, true> */
    private array $instituciones = [];

    /** @var array<string, array{nombre: string, tipo: string, cantidad: int, beneficiarios: int, aporte: float}> */
    private array $items = [];

    /**
     * @param  Registro  $registro
     */
    public function agregar(array $registro): void
    {
        $this->expedientes++;
        $this->bienes += $registro['bienes'];
        $this->aporte += $registro['aporte'];
        $this->beneficiarios += $registro['beneficiarios'];
        $this->acciones += $registro['acciones'];
        $this->participantes += $registro['participantes'];
        $this->investigaciones += $registro['investigaciones'];
        $this->estudiantes[$registro['estudiante']] = true;

        foreach ($registro['instituciones'] as $institucion) {
            $this->instituciones[$institucion] = true;
        }

        foreach ($registro['items'] as $clave => $item) {
            $this->items[$clave] ??= ['nombre' => $item['nombre'], 'tipo' => $item['tipo'], 'cantidad' => 0, 'beneficiarios' => 0, 'aporte' => 0.0];
            $this->items[$clave]['cantidad'] += $item['cantidad'];
            $this->items[$clave]['beneficiarios'] += $item['beneficiarios'];
            $this->items[$clave]['aporte'] += $item['aporte'];
        }
    }

    /**
     * @return array{eps: int, estudiantes: int, bienes_servicios: int, aporte: float, beneficiarios: int, acciones: int, participantes: int, investigaciones: int, instituciones: int}
     */
    public function metricas(): array
    {
        return [
            'eps' => $this->expedientes,
            'estudiantes' => count($this->estudiantes),
            'bienes_servicios' => $this->bienes,
            'aporte' => round($this->aporte, 2),
            'beneficiarios' => $this->beneficiarios,
            'acciones' => $this->acciones,
            'participantes' => $this->participantes,
            'investigaciones' => $this->investigaciones,
            'instituciones' => count($this->instituciones),
        ];
    }

    /**
     * Bienes y servicios ordenados del más al menos frecuente.
     *
     * @return list<array{nombre: string, tipo: string, cantidad: int, beneficiarios: int, aporte: float}>
     */
    public function items(?int $limite = null): array
    {
        $items = collect($this->items)
            ->map(fn (array $item): array => [...$item, 'aporte' => round($item['aporte'], 2)])
            ->sortBy([['cantidad', 'desc'], ['nombre', 'asc']])
            ->values();

        return ($limite === null ? $items : $items->take($limite))->all();
    }
}
