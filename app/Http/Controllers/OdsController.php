<?php

namespace App\Http\Controllers;

use App\Actions\Estadisticas\CalcularEstadisticas;
use App\Concerns\LeeFiltrosEstadisticos;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OdsController extends Controller
{
    use LeeFiltrosEstadisticos;

    /**
     * Lo que aportan los EPS aprobados a cada Objetivo de Desarrollo Sostenible, con los mismos filtros
     * que las estadísticas.
     */
    public function index(Request $request, CalcularEstadisticas $estadisticas): Response
    {
        $filtros = $this->filtros($request, $estadisticas);

        return Inertia::render('ods/index', [
            ...$estadisticas->porOds($filtros),
            ...$estadisticas->opciones($filtros),
            'filtros' => $this->paraCliente($filtros),
        ]);
    }
}
