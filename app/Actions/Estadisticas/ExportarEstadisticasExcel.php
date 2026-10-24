<?php

namespace App\Actions\Estadisticas;

use App\Enums\FuncionUniversitaria;
use App\Enums\MetricaEstadistica;
use App\Models\Departamento;
use App\Support\Articulacion;
use App\Support\LibroExcel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Libros de Excel con lo que muestran las estadísticas y la pestaña de ODS, con los mismos filtros.
 *
 * @phpstan-import-type Filtros from CalcularEstadisticas
 */
class ExportarEstadisticasExcel
{
    public function __construct(private CalcularEstadisticas $estadisticas) {}

    /**
     * Todo el país: resumen, departamentos, EPS, articulación, bienes y servicios y poblaciones vulnerables.
     *
     * @param  Filtros  $filtros
     * @param  list<array{0: string, 1: string}>  $encabezado
     */
    public function pais(array $filtros, array $encabezado): BinaryFileResponse
    {
        $resumen = $this->estadisticas->resumen($filtros);
        $expedientes = $this->estadisticas->expedientesDetallados($filtros);

        return (new LibroExcel)
            ->hoja('Resumen', [['Métrica'], ['Valor']], $this->filasDeMetricas($resumen['totales']), $encabezado)
            ->hoja('Departamentos', [['Código'], ['Departamento'], ['Cabecera'], ...$this->columnasDeMetricas()], array_map(
                fn (array $departamento): array => [$departamento['codigo'], $departamento['nombre'], $departamento['cabecera'], ...$this->valoresDeMetricas($departamento['metricas'])],
                $resumen['departamentos'],
            ))
            ->hoja('EPS', $this->columnasDeEps(), array_map(fn (array $eps): array => $this->filaDeEps($eps), $expedientes))
            ->hoja('Articulación', [['Nivel'], ['EPS', 'entero'], ['% de los EPS', 'porcentaje']], $this->filasDeArticulacion($expedientes))
            ->hoja('Funciones articuladas', [['Función'], ['Evidencia'], ['EPS que la articulan', 'entero'], ['% de los EPS', 'porcentaje']], $this->filasDeFunciones($expedientes))
            ->hoja('Poblaciones vulnerables', $this->columnasDePoblaciones(), $this->filasDePoblaciones($filtros))
            ->descargar('estadisticas-sirse-usac-'.now()->format('d-m-Y').'.xlsx');
    }

    /**
     * Un departamento, organizado por municipio.
     *
     * @param  Filtros  $filtros
     * @param  list<array{0: string, 1: string}>  $encabezado
     */
    public function departamento(Departamento $departamento, array $filtros, array $encabezado): BinaryFileResponse
    {
        $detalle = $this->estadisticas->departamento($departamento, $filtros);
        $municipios = collect($detalle['municipios']);
        $expedientes = array_values(array_filter(
            $this->estadisticas->expedientesDetallados($filtros),
            fn (array $eps): bool => $eps['departamento_codigo'] === $departamento->codigo,
        ));

        return (new LibroExcel)
            ->hoja('Resumen', [['Métrica'], ['Valor']], $this->filasDeMetricas($detalle['metricas']), [['Departamento', $departamento->nombre], ...$encabezado])
            ->hoja('Municipios', [['Municipio'], ...$this->columnasDeMetricas()], $municipios->map(
                fn (array $municipio): array => [$municipio['nombre'], ...$this->valoresDeMetricas($municipio['metricas'])],
            )->all())
            ->hoja('Bienes y servicios', [['Municipio'], ['Bien o servicio'], ['Tipo'], ['Cantidad', 'entero'], ['Beneficiarios', 'entero'], ['Aporte', 'quetzales']], $municipios->flatMap(
                fn (array $municipio): array => array_map(fn (array $item): array => [
                    $municipio['nombre'], $item['nombre'], $item['tipo'] === 'bien' ? 'Bien' : 'Servicio', $item['cantidad'], $item['beneficiarios'], $item['aporte'],
                ], $municipio['bienes_servicios']),
            )->all())
            ->hoja('Investigaciones', [['Municipio'], ['Título'], ['Tipo'], ['Autores'], ['Medio'], ['Fecha'], ['Carrera'], ['Unidad académica'], ['Enlace']], $municipios->flatMap(
                fn (array $municipio): array => array_map(fn (array $investigacion): array => [
                    $municipio['nombre'], Str::squish($investigacion['titulo']), $investigacion['tipo'], Str::squish($investigacion['autores']), $investigacion['medio'],
                    $investigacion['fecha'] === null ? null : Carbon::parse($investigacion['fecha'])->format('d-m-Y'), $investigacion['carrera'], $investigacion['unidad'], $investigacion['enlace'],
                ], $municipio['investigaciones']),
            )->all())
            ->hoja('Instituciones aliadas', [['Municipio'], ['Institución'], ['Tipo'], ['EPS', 'entero']], $municipios->flatMap(
                fn (array $municipio): array => array_map(fn (array $institucion): array => [
                    $municipio['nombre'], $institucion['nombre'], $institucion['tipo'], $institucion['eps'],
                ], $municipio['instituciones']),
            )->all())
            ->hoja('EPS', $this->columnasDeEps(), array_map(fn (array $eps): array => $this->filaDeEps($eps), $expedientes))
            ->descargar('estadisticas-'.Str::slug($departamento->nombre).'-'.now()->format('d-m-Y').'.xlsx');
    }

    /**
     * Lo que aportan los EPS a cada ODS, con todos sus bienes y servicios, líneas, áreas y unidades.
     *
     * @param  Filtros  $filtros
     * @param  list<array{0: string, 1: string}>  $encabezado
     */
    public function ods(array $filtros, array $encabezado): BinaryFileResponse
    {
        $datos = $this->estadisticas->porOds($filtros, null);
        $ods = collect($datos['ods']);
        $detalle = fn (string $grupo, bool $conTipo = false): array => $ods->flatMap(
            fn (array $objetivo): array => array_map(fn (array $fila): array => array_values(array_filter([
                $objetivo['numero'], $objetivo['nombre'], $fila['nombre'],
                $conTipo ? ($fila['tipo'] === 'bien' ? 'Bien' : 'Servicio') : false,
                $fila['cantidad'], $fila['beneficiarios'], $fila['aporte'],
            ], fn (mixed $valor): bool => $valor !== false)), $objetivo[$grupo]),
        )->all();
        $columnasDetalle = fn (string $titulo, bool $conTipo = false): array => array_values(array_filter([
            ['ODS', 'entero'], ['Objetivo'], [$titulo], $conTipo ? ['Tipo'] : null, ['Cantidad', 'entero'], ['Beneficiarios', 'entero'], ['Aporte', 'quetzales'],
        ]));

        return (new LibroExcel)
            ->hoja('ODS', [['ODS', 'entero'], ['Objetivo'], ['Aporte', 'quetzales'], ['% del aporte', 'porcentaje'], ['Bienes y servicios', 'entero'], ['Beneficiarios', 'entero'], ['EPS', 'entero'], ['Estudiantes', 'entero'], ['Departamentos', 'entero']], $ods->map(fn (array $objetivo): array => [
                $objetivo['numero'], $objetivo['nombre'], $objetivo['aporte'],
                $datos['totales']['aporte'] > 0 ? $objetivo['aporte'] / $datos['totales']['aporte'] : 0,
                $objetivo['bienes_servicios'], $objetivo['beneficiarios'], $objetivo['eps'], $objetivo['estudiantes'], $objetivo['departamentos'],
            ])->all(), [
                ...$encabezado,
                ['Aporte total', 'Q'.number_format($datos['totales']['aporte'], 2)],
                ['ODS con aporte', "{$datos['totales']['ods_con_actividad']} de {$ods->count()}"],
                ['Bienes y servicios sin ODS', number_format($datos['sin_ods']['bienes_servicios'])],
            ])
            ->hoja('Bienes y servicios por ODS', $columnasDetalle('Bien o servicio', true), $detalle('items', true))
            ->hoja('Líneas por ODS', $columnasDetalle('Línea de intervención'), $detalle('lineas'))
            ->hoja('Áreas por ODS', $columnasDetalle('Área de intervención'), $detalle('areas'))
            ->hoja('Unidades por ODS', $columnasDetalle('Unidad académica'), $detalle('unidades'))
            ->descargar('ods-sirse-usac-'.now()->format('d-m-Y').'.xlsx');
    }

    /**
     * @return list<array{0: string, 1: 'entero'|'quetzales'}>
     */
    private function columnasDeMetricas(): array
    {
        return array_map(fn (MetricaEstadistica $metrica): array => [$metrica->etiqueta(), $metrica === MetricaEstadistica::Aporte ? 'quetzales' : 'entero'], MetricaEstadistica::cases());
    }

    /**
     * @param  array<string, int|float>  $metricas
     * @return list<int|float>
     */
    private function valoresDeMetricas(array $metricas): array
    {
        return array_map(fn (MetricaEstadistica $metrica): int|float => $metricas[$metrica->value] ?? 0, MetricaEstadistica::cases());
    }

    /**
     * Métricas del resumen ya escritas (Q para el aporte, separador de miles para lo demás): conteos y
     * quetzales comparten columna. Las demás hojas llevan los números para calcular.
     *
     * @param  array<string, int|float>  $metricas
     * @return list<array{0: string, 1: string}>
     */
    private function filasDeMetricas(array $metricas): array
    {
        return array_map(fn (MetricaEstadistica $metrica): array => [$metrica->etiqueta(), $metrica->formatear($metricas[$metrica->value] ?? 0)], MetricaEstadistica::cases());
    }

    /**
     * @return list<array{0: string, 1?: 'texto'|'entero'|'quetzales'|'decimal'|'porcentaje'}>
     */
    private function columnasDeEps(): array
    {
        return [
            ['Estudiante'], ['Carrera'], ['Unidad académica'], ['Programa'], ['Aprobado'], ['Departamento'], ['Municipio'],
            ['Bienes y servicios', 'entero'], ['Beneficiarios', 'entero'], ['Aporte', 'quetzales'], ['Investigaciones', 'entero'],
            ['Acciones de transferencia', 'entero'], ['Participantes', 'entero'], ['Instituciones aliadas', 'entero'],
            ['Articulación (de '.Articulacion::total().')', 'entero'], ['Nivel de articulación'], ['Funciones articuladas'],
        ];
    }

    /**
     * @param  array<string, mixed>  $eps
     * @return list<string|int|float|null>
     */
    private function filaDeEps(array $eps): array
    {
        /** @var Articulacion $articulacion */
        $articulacion = $eps['articulacion'];

        return [
            $eps['estudiante'], $eps['carrera'], $eps['unidad'], $eps['programa'], $eps['aprobado'], $eps['departamento'], $eps['municipio'],
            $eps['bienes_servicios'], $eps['beneficiarios'], $eps['aporte'], $eps['investigaciones'], $eps['acciones'], $eps['participantes'], $eps['instituciones'],
            $articulacion->nivel(), $articulacion->etiqueta(),
            implode(', ', array_map(fn (FuncionUniversitaria $funcion): string => $funcion->etiqueta(), $articulacion->funciones)),
        ];
    }

    /**
     * Cuántos EPS hay en cada nivel de articulación.
     *
     * @param  list<array<string, mixed>>  $expedientes
     * @return list<array{0: string, 1: int, 2: float}>
     */
    private function filasDeArticulacion(array $expedientes): array
    {
        $total = max(1, count($expedientes));
        $porNivel = collect($expedientes)->countBy(fn (array $eps): int => $eps['articulacion']->nivel());

        return array_map(fn (int $nivel): array => [
            "{$nivel} de ".Articulacion::total().' · '.(new Articulacion(array_slice(FuncionUniversitaria::cases(), 0, $nivel)))->etiqueta(),
            $porNivel->get($nivel, 0),
            $porNivel->get($nivel, 0) / $total,
        ], range(Articulacion::total(), 0));
    }

    /**
     * Cuántos EPS articulan cada función.
     *
     * @param  list<array<string, mixed>>  $expedientes
     * @return list<array{0: string, 1: string, 2: int, 3: float}>
     */
    private function filasDeFunciones(array $expedientes): array
    {
        $total = max(1, count($expedientes));

        return array_map(function (FuncionUniversitaria $funcion) use ($expedientes, $total): array {
            $conFuncion = count(array_filter($expedientes, fn (array $eps): bool => in_array($funcion, $eps['articulacion']->funciones, true)));

            return [$funcion->etiqueta(), $funcion->evidencia(), $conFuncion, $conFuncion / $total];
        }, FuncionUniversitaria::cases());
    }

    /**
     * @return list<array{0: string, 1?: 'entero'|'quetzales'}>
     */
    private function columnasDePoblaciones(): array
    {
        return [['Población vulnerable'], ['EPS', 'entero'], ['Bienes y servicios registrados', 'entero'], ['Cantidad entregada', 'entero'], ['Beneficiarios', 'entero'], ['Aporte', 'quetzales']];
    }

    /**
     * @param  Filtros  $filtros
     * @return list<list<string|int|float>>
     */
    private function filasDePoblaciones(array $filtros): array
    {
        return array_map(fn (array $poblacion): array => [
            $poblacion['nombre'], $poblacion['eps'], $poblacion['registros'], $poblacion['cantidad'], $poblacion['beneficiarios'], $poblacion['aporte'],
        ], $this->estadisticas->poblacionesVulnerables($filtros));
    }
}
