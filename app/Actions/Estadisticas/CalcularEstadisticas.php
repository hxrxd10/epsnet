<?php

namespace App\Actions\Estadisticas;

use App\Models\Adjunto;
use App\Models\Alianza;
use App\Models\BienServicio;
use App\Models\Catalogo;
use App\Models\Departamento;
use App\Models\Expediente;
use App\Models\Municipio;
use App\Models\PublicacionInvestigacion;
use App\Models\TransferenciaConocimiento;
use App\Models\UbicacionTerritorial;
use App\Models\UnidadAcademica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Consolida los EPS completos o verificados en estadísticas por departamento y municipio.
 *
 * El año de un EPS es el de su orden de impresión y todo lo que registró se atribuye a su primera
 * ubicación territorial.
 *
 * @phpstan-type Filtros array{anio: int|null, unidad: int|null, carrera: string|null}
 *
 * @phpstan-import-type Registro from Acumulador
 */
class CalcularEstadisticas
{
    private const string SIN_CLASIFICAR = 'Sin clasificar';

    private const string SIN_MUNICIPIO = 'Sin municipio';

    /**
     * Estadísticas de todo el país para pintar el mapa.
     *
     * @param  Filtros  $filtros
     * @return array{totales: array<string, int>, sin_ubicacion: array<string, int>, departamentos: list<array<string, mixed>>}
     */
    public function resumen(array $filtros): array
    {
        $registros = $this->registros($this->expedientes($filtros));
        $ubicaciones = $this->ubicaciones($this->expedientes($filtros));

        $total = new Acumulador;
        $sinUbicacion = new Acumulador;
        /** @var array<int, Acumulador> $porDepartamento */
        $porDepartamento = [];

        foreach ($registros as $expedienteId => $registro) {
            $total->agregar($registro);

            $departamentoId = $ubicaciones[$expedienteId]->departamento_id ?? null;

            if ($departamentoId === null) {
                $sinUbicacion->agregar($registro);

                continue;
            }

            ($porDepartamento[$departamentoId] ??= new Acumulador)->agregar($registro);
        }

        return [
            'totales' => $total->metricas(),
            'sin_ubicacion' => $sinUbicacion->metricas(),
            'departamentos' => Departamento::query()->orderBy('codigo')->get()->map(function (Departamento $departamento) use ($porDepartamento): array {
                $acumulador = $porDepartamento[$departamento->id] ?? new Acumulador;

                return [
                    'codigo' => $departamento->codigo,
                    'nombre' => $departamento->nombre,
                    'cabecera' => $departamento->cabecera,
                    'metricas' => $acumulador->metricas(),
                    'top_bienes_servicios' => $acumulador->items(5),
                ];
            })->all(),
        ];
    }

    /**
     * Todo lo consolidado de un departamento, organizado por municipio.
     *
     * @param  Filtros  $filtros
     * @return array{metricas: array<string, int>, municipios: list<array<string, mixed>>}
     */
    public function departamento(Departamento $departamento, array $filtros): array
    {
        $base = $this->expedientes($filtros);
        $registros = $this->registros($base);
        $ubicaciones = $this->ubicaciones($this->expedientes($filtros))
            ->filter(fn (UbicacionTerritorial $ubicacion): bool => $ubicacion->departamento_id === $departamento->id);

        $registros = $registros->only($ubicaciones->keys()->all());
        $expedientes = $this->expedientes($filtros)->whereKey($registros->keys()->all())->get(['id', 'nombre_carrera', 'nombre_unidad'])->keyBy('id');

        $publicaciones = PublicacionInvestigacion::query()
            ->whereIn('expediente_id', $registros->keys()->all())
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('id')
            ->get()
            ->groupBy('expediente_id');

        $aliadas = Alianza::query()
            ->whereIn('expediente_id', $registros->keys()->all())
            ->with('institucionAliada:id,nombre,tipo')
            ->get()
            ->groupBy('expediente_id');

        $nombres = Municipio::query()->where('departamento_id', $departamento->id)->pluck('nombre', 'id');

        $total = new Acumulador;
        /** @var array<int, array{nombre: string, acumulador: Acumulador, investigaciones: list<array<string, mixed>>, instituciones: array<int, array{nombre: string, tipo: string|null, eps: int}>}> $municipios */
        $municipios = [];

        foreach ($registros as $expedienteId => $registro) {
            $total->agregar($registro);

            $clave = (int) $ubicaciones[$expedienteId]->municipio_id;
            $municipios[$clave] ??= ['nombre' => $nombres[$clave] ?? self::SIN_MUNICIPIO, 'acumulador' => new Acumulador, 'investigaciones' => [], 'instituciones' => []];
            $municipios[$clave]['acumulador']->agregar($registro);

            foreach ($aliadas->get($expedienteId, []) as $alianza) {
                $municipios[$clave]['instituciones'][$alianza->institucion_aliada_id] ??= ['nombre' => $alianza->institucionAliada->nombre, 'tipo' => $alianza->institucionAliada->tipo, 'eps' => 0];
                $municipios[$clave]['instituciones'][$alianza->institucion_aliada_id]['eps']++;
            }

            foreach ($publicaciones->get($expedienteId, []) as $publicacion) {
                $municipios[$clave]['investigaciones'][] = [
                    'id' => $publicacion->id,
                    'titulo' => $publicacion->titulo,
                    'tipo' => $publicacion->tipo->etiqueta(),
                    'autores' => $publicacion->autores,
                    'medio' => $publicacion->medio_publicacion,
                    'fecha' => $publicacion->fecha_publicacion?->toDateString(),
                    'enlace' => $publicacion->enlace,
                    'carrera' => $expedientes[$expedienteId]->nombre_carrera,
                    'unidad' => $expedientes[$expedienteId]->nombre_unidad,
                ];
            }
        }

        return [
            'metricas' => $total->metricas(),
            'municipios' => collect($municipios)
                ->sortBy(fn (array $municipio): string => Str::ascii($municipio['nombre']), SORT_NATURAL | SORT_FLAG_CASE)
                ->map(fn (array $municipio): array => [
                    'nombre' => $municipio['nombre'],
                    'metricas' => $municipio['acumulador']->metricas(),
                    'bienes_servicios' => $municipio['acumulador']->items(),
                    'investigaciones' => $municipio['investigaciones'],
                    'instituciones' => collect($municipio['instituciones'])->sortBy([['eps', 'desc'], ['nombre', 'asc']])->values()->all(),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * Valores que ofrecen los filtros. Las carreras se limitan a la unidad elegida.
     *
     * @param  Filtros  $filtros
     * @return array{anios: list<int>, unidades: list<array{value: string, label: string}>, carreras: list<array{value: string, label: string}>}
     */
    public function opciones(array $filtros): array
    {
        $ordenes = Adjunto::query()
            ->where('categoria', Adjunto::ORDEN_IMPRESION)
            ->where('entidad_tipo', 'expediente')
            ->whereIn('entidad_id', Expediente::query()->validos()->select('id'));
        $sinOrden = Expediente::query()->validos()->whereDoesntHave('ordenImpresion')->whereNotNull('verificado_at');
        $fechas = array_filter([
            $ordenes->min('fecha_subida'), $ordenes->max('fecha_subida'),
            $sinOrden->min('verificado_at'), $sinOrden->max('verificado_at'),
        ]);
        $anios = array_map(fn (string $fecha): int => Carbon::parse($fecha)->year, $fechas);

        return [
            'anios' => $anios === [] ? [] : range(max($anios), min($anios)),
            'unidades' => UnidadAcademica::query()
                ->whereIn('id', Expediente::query()->validos()->select('unidad_academica_id'))
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn (UnidadAcademica $unidad): array => ['value' => (string) $unidad->id, 'label' => $unidad->nombre])
                ->all(),
            'carreras' => $this->carreras($filtros['unidad'])
                ->map(fn (string $carrera): array => ['value' => $carrera, 'label' => $carrera])
                ->all(),
        ];
    }

    /**
     * Carreras con EPS, opcionalmente solo las de una unidad.
     *
     * @return Collection<int, string>
     */
    public function carreras(?int $unidad): Collection
    {
        return Expediente::query()
            ->validos()
            ->when($unidad, fn (Builder $consulta, int $unidad) => $consulta->where('unidad_academica_id', $unidad))
            ->distinct()
            ->orderBy('nombre_carrera')
            ->pluck('nombre_carrera');
    }

    /**
     * @param  Filtros  $filtros
     * @return Builder<Expediente>
     */
    private function expedientes(array $filtros): Builder
    {
        return Expediente::query()
            ->validos()
            ->when($filtros['unidad'], fn (Builder $consulta, int $unidad) => $consulta->where('unidad_academica_id', $unidad))
            ->when($filtros['carrera'], fn (Builder $consulta, string $carrera) => $consulta->where('nombre_carrera', $carrera))
            ->when($filtros['anio'], fn (Builder $consulta, int $anio) => $consulta->where(function (Builder $consulta) use ($anio): void {
                $desde = "{$anio}-01-01";
                $hasta = ($anio + 1).'-01-01';

                // El año es el de la orden de impresión; si el EPS no la tiene, el de su aprobación.
                $consulta
                    ->whereHas('ordenImpresion', fn (Builder $orden) => $orden->where('fecha_subida', '>=', $desde)->where('fecha_subida', '<', $hasta))
                    ->orWhere(fn (Builder $sinOrden) => $sinOrden->whereDoesntHave('ordenImpresion')->where('verificado_at', '>=', $desde)->where('verificado_at', '<', $hasta));
            }));
    }

    /**
     * Primera ubicación de cada expediente.
     *
     * @param  Builder<Expediente>  $expedientes
     * @return Collection<int, UbicacionTerritorial>
     */
    private function ubicaciones(Builder $expedientes): Collection
    {
        return UbicacionTerritorial::query()
            ->whereIn('expediente_id', $expedientes->select('id'))
            ->orderBy('id')
            ->get(['id', 'expediente_id', 'departamento_id', 'municipio_id'])
            ->unique('expediente_id')
            ->keyBy('expediente_id');
    }

    /**
     * Lo registrado en los ejes, resumido por expediente con consultas agregadas.
     *
     * @param  Builder<Expediente>  $expedientes
     * @return Collection<int, Registro>
     */
    private function registros(Builder $expedientes): Collection
    {
        $base = $expedientes->get(['id', 'estudiante_id'])->keyBy('id');
        $ids = $base->keys()->all();

        $bienes = BienServicio::query()
            ->whereIn('expediente_id', $ids)
            ->selectRaw('expediente_id, catalogo_id, tipo, count(*) as total, coalesce(sum(cantidad_beneficiarios), 0) as beneficiarios')
            ->groupBy('expediente_id', 'catalogo_id', 'tipo')
            ->get()
            ->groupBy('expediente_id');
        $catalogo = Catalogo::query()
            ->whereIn('id', $bienes->flatten()->pluck('catalogo_id')->filter()->unique())
            ->pluck('nombre', 'id');
        $acciones = TransferenciaConocimiento::query()
            ->whereIn('expediente_id', $ids)
            ->selectRaw('expediente_id, count(*) as total, coalesce(sum(numero_participantes), 0) as participantes')
            ->groupBy('expediente_id')
            ->get()
            ->keyBy('expediente_id');
        $investigaciones = PublicacionInvestigacion::query()
            ->whereIn('expediente_id', $ids)
            ->selectRaw('expediente_id, count(*) as total')
            ->groupBy('expediente_id')
            ->pluck('total', 'expediente_id');
        $instituciones = Alianza::query()
            ->whereIn('expediente_id', $ids)
            ->select('expediente_id', 'institucion_aliada_id')
            ->distinct()
            ->get()
            ->groupBy('expediente_id');

        return $base->map(function (Expediente $expediente) use ($bienes, $catalogo, $acciones, $investigaciones, $instituciones): array {
            $items = [];

            foreach ($bienes->get($expediente->id, []) as $fila) {
                $nombre = $catalogo[$fila->catalogo_id] ?? self::SIN_CLASIFICAR;
                $clave = "{$fila->tipo->value}|{$nombre}";
                $items[$clave] ??= ['nombre' => $nombre, 'tipo' => $fila->tipo->value, 'cantidad' => 0, 'beneficiarios' => 0];
                $items[$clave]['cantidad'] += (int) $fila->total;
                $items[$clave]['beneficiarios'] += (int) $fila->beneficiarios;
            }

            return [
                'estudiante' => $expediente->estudiante_id,
                'bienes' => array_sum(array_column($items, 'cantidad')),
                'beneficiarios' => array_sum(array_column($items, 'beneficiarios')),
                'acciones' => (int) ($acciones[$expediente->id]->total ?? 0),
                'participantes' => (int) ($acciones[$expediente->id]->participantes ?? 0),
                'investigaciones' => (int) ($investigaciones[$expediente->id] ?? 0),
                'instituciones' => $instituciones->get($expediente->id, collect())->pluck('institucion_aliada_id')->all(),
                'items' => $items,
            ];
        });
    }
}
