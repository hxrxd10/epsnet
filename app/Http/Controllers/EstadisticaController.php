<?php

namespace App\Http\Controllers;

use App\Actions\Estadisticas\CalcularEstadisticas;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EstadisticaController extends Controller
{
    /**
     * Mapa de Guatemala con las estadísticas consolidadas por departamento.
     */
    public function index(Request $request, CalcularEstadisticas $estadisticas): Response
    {
        $filtros = $this->filtros($request, $estadisticas);

        return Inertia::render('estadisticas/index', [
            ...$estadisticas->resumen($filtros),
            ...$estadisticas->opciones($filtros),
            'filtros' => $this->paraCliente($filtros),
        ]);
    }

    /**
     * Todo lo consolidado de un departamento, organizado por municipio.
     */
    public function departamento(Request $request, Departamento $departamento, CalcularEstadisticas $estadisticas): Response
    {
        $filtros = $this->filtros($request, $estadisticas);

        return Inertia::render('estadisticas/departamento', [
            'departamento' => $departamento->only(['codigo', 'nombre', 'cabecera']),
            ...$estadisticas->departamento($departamento, $filtros),
            ...$estadisticas->opciones($filtros),
            'filtros' => $this->paraCliente($filtros),
        ]);
    }

    /**
     * Filtros validados; una carrera que no pertenece a la unidad elegida se descarta.
     *
     * @return array{anio: int|null, unidad: int|null, carrera: string|null}
     */
    private function filtros(Request $request, CalcularEstadisticas $estadisticas): array
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
     * @param  array{anio: int|null, unidad: int|null, carrera: string|null}  $filtros
     * @return array{anio: string, unidad: string, carrera: string}
     */
    private function paraCliente(array $filtros): array
    {
        return [
            'anio' => (string) ($filtros['anio'] ?? ''),
            'unidad' => (string) ($filtros['unidad'] ?? ''),
            'carrera' => $filtros['carrera'] ?? '',
        ];
    }
}
