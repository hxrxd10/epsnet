<?php

namespace App\Http\Controllers;

use App\Actions\Estadisticas\CalcularEstadisticas;
use App\Concerns\LeeFiltrosEstadisticos;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EstadisticaController extends Controller
{
    use LeeFiltrosEstadisticos;

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
}
