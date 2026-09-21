<?php

namespace App\Http\Controllers;

use App\Actions\Estadisticas\CalcularEstadisticas;
use App\Concerns\LeeFiltrosEstadisticos;
use App\Enums\MetricaEstadistica;
use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\Departamento;
use App\Models\UnidadAcademica;
use App\Support\MapaEstadisticoSvg;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class EstadisticaPdfController extends Controller
{
    use LeeFiltrosEstadisticos;

    /**
     * PDF de las estadísticas del país con los filtros y la métrica que se ven en pantalla.
     */
    public function index(Request $request, CalcularEstadisticas $estadisticas): Response
    {
        $filtros = $this->filtros($request, $estadisticas);
        $metrica = $this->metrica($request);
        $resumen = $estadisticas->resumen($filtros);
        $departamentos = collect($resumen['departamentos']);
        $valores = $departamentos->mapWithKeys(fn (array $departamento): array => [$departamento['codigo'] => $departamento['metricas'][$metrica->value]])->all();

        Bitacora::registrar('Estadísticas', TipoCambioBitacora::Exportacion, "Generó el PDF de estadísticas del país ({$metrica->etiqueta()}) · ".$this->resumenDeFiltros($filtros));

        return Pdf::loadView('pdf.estadisticas', [
            ...$this->datosComunes($request, $filtros),
            'metricaElegida' => $metrica,
            'totales' => $resumen['totales'],
            'sinUbicacion' => $resumen['sin_ubicacion'],
            'departamentos' => $departamentos->all(),
            'ranking' => $departamentos->filter(fn (array $departamento): bool => $departamento['metricas'][$metrica->value] > 0)
                ->sortByDesc(fn (array $departamento): int => $departamento['metricas'][$metrica->value])
                ->take(12)->values()->all(),
            'conBienes' => $departamentos->filter(fn (array $departamento): bool => $departamento['top_bienes_servicios'] !== [])->values()->all(),
            'mapa' => MapaEstadisticoSvg::dataUri($valores),
        ])->download('estadisticas-epsnet-'.now()->format('Ymd').'.pdf');
    }

    /**
     * PDF de un departamento con todo lo consolidado, organizado por municipio.
     */
    public function departamento(Request $request, Departamento $departamento, CalcularEstadisticas $estadisticas): Response
    {
        $filtros = $this->filtros($request, $estadisticas);
        $detalle = $estadisticas->departamento($departamento, $filtros);

        Bitacora::registrar('Estadísticas', TipoCambioBitacora::Exportacion, "Generó el PDF del departamento {$departamento->nombre} · ".$this->resumenDeFiltros($filtros));

        return Pdf::loadView('pdf.departamento', [
            ...$this->datosComunes($request, $filtros),
            'departamento' => $departamento,
            'totalesDepartamento' => $detalle['metricas'],
            'municipios' => $detalle['municipios'],
            'mapa' => MapaEstadisticoSvg::dataUri([], $departamento->codigo, 300),
        ])->download('estadisticas-'.Str::slug($departamento->nombre).'-'.now()->format('Ymd').'.pdf');
    }

    private function metrica(Request $request): MetricaEstadistica
    {
        $datos = $request->validate(['metrica' => ['nullable', Rule::enum(MetricaEstadistica::class)]]);

        return MetricaEstadistica::tryFrom($datos['metrica'] ?? '') ?? MetricaEstadistica::Investigaciones;
    }

    /**
     * Lo que comparten los dos PDF: logo, filtros aplicados, quién y cuándo lo generó.
     *
     * @param  array{anio: int|null, unidad: int|null, carrera: string|null}  $filtros
     * @return array<string, mixed>
     */
    private function datosComunes(Request $request, array $filtros): array
    {
        return [
            'logo' => 'data:image/png;base64,'.base64_encode((string) file_get_contents(public_path('images/logo.png'))),
            'filtros' => $this->etiquetasDeFiltros($filtros),
            'metricas' => MetricaEstadistica::cases(),
            'generadoPor' => "{$request->user()->name} ({$request->user()->email})",
            'fecha' => now(),
        ];
    }

    /**
     * @param  array{anio: int|null, unidad: int|null, carrera: string|null}  $filtros
     * @return array<string, string>
     */
    private function etiquetasDeFiltros(array $filtros): array
    {
        return [
            'Año' => $filtros['anio'] === null ? 'Todos los años' : (string) $filtros['anio'],
            'Unidad académica' => $filtros['unidad'] === null ? 'Todas las unidades' : (UnidadAcademica::find($filtros['unidad'])?->nombre ?? '—'),
            'Carrera' => $filtros['carrera'] ?? 'Todas las carreras',
        ];
    }

    /**
     * @param  array{anio: int|null, unidad: int|null, carrera: string|null}  $filtros
     */
    private function resumenDeFiltros(array $filtros): string
    {
        return collect($this->etiquetasDeFiltros($filtros))->map(fn (string $valor, string $nombre): string => "{$nombre}: {$valor}")->implode(' · ');
    }
}
