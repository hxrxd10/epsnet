<?php

namespace App\Http\Controllers;

use App\Actions\Estadisticas\CalcularEstadisticas;
use App\Actions\Estadisticas\ExportarEstadisticasExcel;
use App\Concerns\LeeFiltrosEstadisticos;
use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EstadisticaExcelController extends Controller
{
    use LeeFiltrosEstadisticos;

    /**
     * Excel de las estadísticas del país con los filtros que se ven en pantalla.
     */
    public function index(Request $request, CalcularEstadisticas $estadisticas, ExportarEstadisticasExcel $excel): BinaryFileResponse
    {
        $filtros = $this->filtros($request, $estadisticas);

        Bitacora::registrar('Estadísticas', TipoCambioBitacora::Exportacion, 'Exportó a Excel las estadísticas del país · '.$this->resumenDeFiltros($filtros));

        return $excel->pais($filtros, $this->encabezado($request, $filtros));
    }

    /**
     * Excel de un departamento, organizado por municipio.
     */
    public function departamento(Request $request, Departamento $departamento, CalcularEstadisticas $estadisticas, ExportarEstadisticasExcel $excel): BinaryFileResponse
    {
        $filtros = $this->filtros($request, $estadisticas);

        Bitacora::registrar('Estadísticas', TipoCambioBitacora::Exportacion, "Exportó a Excel el departamento {$departamento->nombre} · ".$this->resumenDeFiltros($filtros));

        return $excel->departamento($departamento, $filtros, $this->encabezado($request, $filtros));
    }

    /**
     * Filtros aplicados, quién y cuándo lo generó, para el encabezado de la primera hoja.
     *
     * @param  array{anio: int|null, unidad: int|null, carrera: string|null, alcance?: User|null}  $filtros
     * @return list<array{0: string, 1: string}>
     */
    private function encabezado(Request $request, array $filtros): array
    {
        return self::encabezadoDeExportacion($request, $this->etiquetasDeFiltros($filtros));
    }

    /**
     * @param  array<string, string>  $filtros
     * @return list<array{0: string, 1: string}>
     */
    public static function encabezadoDeExportacion(Request $request, array $filtros): array
    {
        return [
            ['SIRSE USAC', 'Sistema Unificado de Registro y Seguimiento del EPS · Solo EPS aprobados por supervisión'],
            ...collect($filtros)->map(fn (string $valor, string $nombre): array => [$nombre, $valor])->values()->all(),
            ['Generado por', "{$request->user()->name} ({$request->user()->email})"],
            ['Fecha', now()->format('d-m-Y H:i')],
        ];
    }
}
