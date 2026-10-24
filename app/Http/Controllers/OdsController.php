<?php

namespace App\Http\Controllers;

use App\Actions\Estadisticas\CalcularEstadisticas;
use App\Actions\Estadisticas\ExportarEstadisticasExcel;
use App\Concerns\LeeFiltrosEstadisticos;
use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    /**
     * Excel de la pestaña de ODS con todos sus bienes y servicios, líneas, áreas y unidades.
     */
    public function excel(Request $request, CalcularEstadisticas $estadisticas, ExportarEstadisticasExcel $excel): BinaryFileResponse
    {
        $filtros = $this->filtros($request, $estadisticas);

        Bitacora::registrar('ODS', TipoCambioBitacora::Exportacion, 'Exportó a Excel el aporte por ODS · '.$this->resumenDeFiltros($filtros));

        return $excel->ods($filtros, EstadisticaExcelController::encabezadoDeExportacion($request, $this->etiquetasDeFiltros($filtros)));
    }
}
