<?php

namespace App\Concerns;

use App\Actions\Estadisticas\CalcularEstadisticas;
use Illuminate\Http\Request;

/**
 * Filtros de las estadísticas (año, unidad académica y carrera) tal como llegan en la solicitud.
 *
 * @phpstan-import-type Filtros from CalcularEstadisticas
 */
trait LeeFiltrosEstadisticos
{
    /**
     * Filtros validados; una carrera que no pertenece a la unidad elegida se descarta.
     *
     * @return Filtros
     */
    protected function filtros(Request $request, CalcularEstadisticas $estadisticas): array
    {
        $datos = $request->validate([
            'anio' => ['nullable', 'integer', 'between:2000,2100'],
            'unidad' => ['nullable', 'integer', 'exists:unidades_academicas,id'],
            'carrera' => ['nullable', 'string', 'max:500'],
        ]);

        $unidad = isset($datos['unidad']) ? (int) $datos['unidad'] : null;
        $carrera = $datos['carrera'] ?? null;

        return [
            'anio' => isset($datos['anio']) ? (int) $datos['anio'] : null,
            'unidad' => $unidad,
            'carrera' => $carrera !== null && $estadisticas->carreras($unidad)->contains($carrera) ? $carrera : null,
        ];
    }

    /**
     * @param  Filtros  $filtros
     * @return array{anio: string, unidad: string, carrera: string}
     */
    protected function paraCliente(array $filtros): array
    {
        return [
            'anio' => (string) ($filtros['anio'] ?? ''),
            'unidad' => (string) ($filtros['unidad'] ?? ''),
            'carrera' => $filtros['carrera'] ?? '',
        ];
    }
}
