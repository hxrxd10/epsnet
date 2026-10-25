<?php

namespace App\Concerns;

use App\Actions\Estadisticas\CalcularEstadisticas;
use App\Models\UnidadAcademica;
use Illuminate\Http\Request;

/**
 * Filtros de las estadísticas (año, unidad académica y carrera) tal como llegan en la solicitud.
 *
 * @phpstan-import-type Filtros from CalcularEstadisticas
 */
trait LeeFiltrosEstadisticos
{
    /**
     * Filtros validados; una carrera que no pertenece a la unidad elegida se descarta. Incluyen a quien
     * consulta, para que una unidad académica solo vea lo de su unidad y sus carreras.
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

        $alcance = $request->user();

        return [
            'anio' => isset($datos['anio']) ? (int) $datos['anio'] : null,
            'unidad' => $unidad,
            'carrera' => $carrera !== null && $estadisticas->carreras($unidad, $alcance)->contains($carrera) ? $carrera : null,
            'alcance' => $alcance,
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

    /**
     * Los filtros como los lee una persona: «Año: 2026», «Unidad académica: Todas las unidades»…
     *
     * @param  Filtros  $filtros
     * @return array<string, string>
     */
    protected function etiquetasDeFiltros(array $filtros): array
    {
        return [
            'Año' => $filtros['anio'] === null ? 'Todos los años' : (string) $filtros['anio'],
            'Unidad académica' => $filtros['unidad'] === null ? 'Todas las unidades' : (UnidadAcademica::find($filtros['unidad'])?->nombre ?? '—'),
            'Carrera' => $filtros['carrera'] ?? 'Todas las carreras',
            ...(($filtros['alcance'] ?? null)?->esUnidadAcademica() ? ['Alcance' => $filtros['alcance']->ambitoDeConsulta()] : []),
        ];
    }

    /**
     * @param  Filtros  $filtros
     */
    protected function resumenDeFiltros(array $filtros): string
    {
        return collect($this->etiquetasDeFiltros($filtros))->map(fn (string $valor, string $nombre): string => "{$nombre}: {$valor}")->implode(' · ');
    }
}
