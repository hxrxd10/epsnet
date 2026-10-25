<?php

namespace App\Actions\Estadisticas;

use App\Enums\TipoCatalogo;
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
use App\Models\User;
use App\Support\Articulacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Consolida los EPS aprobados en estadísticas por departamento y municipio. El aporte de un EPS es la
 * cantidad de cada bien o servicio que entregó por su valor base del catálogo, en quetzales.
 *
 * El año de un EPS es el de su orden de impresión y todo lo que registró se atribuye a su primera
 * ubicación territorial.
 *
 * Con `alcance` (quien consulta), una unidad académica solo cuenta lo de su unidad y sus carreras.
 *
 * @phpstan-type Filtros array{anio: int|null, unidad: int|null, carrera: string|null, alcance?: User|null}
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
     * @return array{totales: array<string, int|float>, sin_ubicacion: array<string, int|float>, departamentos: list<array<string, mixed>>}
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
     * @return array{metricas: array<string, int|float>, municipios: list<array<string, mixed>>}
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
            ->whereIn('entidad_id', $this->validosVisibles($filtros)->select('id'));
        $sinOrden = $this->validosVisibles($filtros)->whereDoesntHave('ordenImpresion')->whereNotNull('verificado_at');
        $fechas = array_filter([
            $ordenes->min('fecha_subida'), $ordenes->max('fecha_subida'),
            $sinOrden->min('verificado_at'), $sinOrden->max('verificado_at'),
        ]);
        $anios = array_map(fn (string $fecha): int => Carbon::parse($fecha)->year, $fechas);

        return [
            'anios' => $anios === [] ? [] : range(max($anios), min($anios)),
            'unidades' => UnidadAcademica::query()
                ->whereIn('id', $this->validosVisibles($filtros)->select('unidad_academica_id'))
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn (UnidadAcademica $unidad): array => ['value' => (string) $unidad->id, 'label' => $unidad->nombre])
                ->all(),
            'carreras' => $this->carreras($filtros['unidad'], $filtros['alcance'] ?? null)
                ->map(fn (string $carrera): array => ['value' => $carrera, 'label' => $carrera])
                ->all(),
        ];
    }

    /**
     * Lo que aportan los EPS aprobados a cada Objetivo de Desarrollo Sostenible, a partir de los bienes y
     * servicios que entregaron (cada elemento del catálogo está anclado a un ODS).
     *
     * @param  Filtros  $filtros
     * @param  int|null  $limite  Cuántos bienes, líneas, áreas y unidades se dan por ODS (null: todos).
     * @return array{totales: array{eps: int, estudiantes: int, bienes_servicios: int, beneficiarios: int, aporte: float, ods_con_actividad: int}, sin_ods: array{bienes_servicios: int, aporte: float}, ods: list<array<string, mixed>>}
     */
    public function porOds(array $filtros, ?int $limite = 10): array
    {
        $expedientes = $this->expedientes($filtros)->get(['id', 'estudiante_id', 'unidad_academica_id'])->keyBy('id');
        $ubicaciones = $this->ubicaciones($this->expedientes($filtros));

        $filas = BienServicio::query()
            ->whereIn('expediente_id', $expedientes->keys()->all())
            ->selectRaw('expediente_id, catalogo_id, tipo, coalesce(sum(coalesce(cantidad, 1)), 0) as total, coalesce(sum(cantidad_beneficiarios), 0) as beneficiarios')
            ->groupBy('expediente_id', 'catalogo_id', 'tipo')
            ->get();

        $catalogo = Catalogo::query()
            ->whereIn('id', $filas->pluck('catalogo_id')->filter()->unique())
            ->with(['lineaIntervencion:id,nombre', 'areaIntervencion:id,nombre'])
            ->get()
            ->keyBy('id');
        $unidades = UnidadAcademica::query()->whereIn('id', $expedientes->pluck('unidad_academica_id')->unique())->pluck('nombre', 'id');

        /** @var array<int, array<string, mixed>> $porOds */
        $porOds = [];
        $sinOds = ['bienes_servicios' => 0, 'aporte' => 0.0];
        $total = ['eps' => [], 'estudiantes' => [], 'bienes_servicios' => 0, 'beneficiarios' => 0, 'aporte' => 0.0];

        foreach ($filas as $fila) {
            $elemento = $catalogo[$fila->catalogo_id] ?? null;
            $cantidad = (int) $fila->total;
            $aporte = $cantidad * (float) ($elemento->valor_base ?? 0);
            $expediente = $expedientes[$fila->expediente_id];

            $total['eps'][$expediente->id] = true;
            $total['estudiantes'][$expediente->estudiante_id] = true;
            $total['bienes_servicios'] += $cantidad;
            $total['beneficiarios'] += (int) $fila->beneficiarios;
            $total['aporte'] += $aporte;

            if ($elemento?->ods_id === null) {
                $sinOds['bienes_servicios'] += $cantidad;
                $sinOds['aporte'] += $aporte;

                continue;
            }

            $clave = $elemento->ods_id;
            $porOds[$clave] ??= ['eps' => [], 'estudiantes' => [], 'departamentos' => [], 'bienes_servicios' => 0, 'beneficiarios' => 0, 'aporte' => 0.0, 'items' => [], 'lineas' => [], 'areas' => [], 'unidades' => []];
            $porOds[$clave]['eps'][$expediente->id] = true;
            $porOds[$clave]['estudiantes'][$expediente->estudiante_id] = true;
            $departamento = $ubicaciones[$expediente->id]->departamento_id ?? null;
            $departamento === null || $porOds[$clave]['departamentos'][$departamento] = true;
            $porOds[$clave]['bienes_servicios'] += $cantidad;
            $porOds[$clave]['beneficiarios'] += (int) $fila->beneficiarios;
            $porOds[$clave]['aporte'] += $aporte;

            $this->sumar($porOds[$clave]['items'], (string) $elemento->id, ['nombre' => $elemento->nombre, 'tipo' => $fila->tipo->value], $cantidad, $aporte, (int) $fila->beneficiarios);
            $this->sumar($porOds[$clave]['lineas'], (string) $elemento->linea_intervencion_id, ['nombre' => $elemento->lineaIntervencion->nombre ?? self::SIN_CLASIFICAR], $cantidad, $aporte, (int) $fila->beneficiarios);
            $this->sumar($porOds[$clave]['areas'], (string) $elemento->area_intervencion_id, ['nombre' => $elemento->areaIntervencion->nombre ?? self::SIN_CLASIFICAR], $cantidad, $aporte, (int) $fila->beneficiarios);
            $this->sumar($porOds[$clave]['unidades'], (string) $expediente->unidad_academica_id, ['nombre' => $unidades[$expediente->unidad_academica_id] ?? self::SIN_CLASIFICAR], $cantidad, $aporte, (int) $fila->beneficiarios);
        }

        $ordenar = fn (array $grupo): array => collect($grupo)
            ->map(fn (array $item): array => [...$item, 'aporte' => round($item['aporte'], 2)])
            ->sortBy([['aporte', 'desc'], ['cantidad', 'desc'], ['nombre', 'asc']])
            ->when($limite !== null, fn (Collection $items) => $items->take($limite))
            ->values()
            ->all();

        $ods = Catalogo::query()
            ->delCatalogo(TipoCatalogo::Ods)
            ->get(['id', 'nombre'])
            ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
            ->map(function (Catalogo $elemento) use ($porOds, $ordenar): array {
                $datos = $porOds[$elemento->id] ?? null;

                return [
                    'id' => $elemento->id,
                    'numero' => preg_match('/\d+/', $elemento->nombre, $numero) === 1 ? (int) $numero[0] : null,
                    'nombre' => trim((string) preg_replace('/^ODS\s*\d+\s*[:.-]?\s*/iu', '', $elemento->nombre)),
                    'eps' => count($datos['eps'] ?? []),
                    'estudiantes' => count($datos['estudiantes'] ?? []),
                    'departamentos' => count($datos['departamentos'] ?? []),
                    'bienes_servicios' => $datos['bienes_servicios'] ?? 0,
                    'beneficiarios' => $datos['beneficiarios'] ?? 0,
                    'aporte' => round($datos['aporte'] ?? 0, 2),
                    'items' => $ordenar($datos['items'] ?? []),
                    'lineas' => $ordenar($datos['lineas'] ?? []),
                    'areas' => $ordenar($datos['areas'] ?? []),
                    'unidades' => $ordenar($datos['unidades'] ?? []),
                ];
            })
            ->values()
            ->all();

        return [
            'totales' => [
                'eps' => count($total['eps']),
                'estudiantes' => count($total['estudiantes']),
                'bienes_servicios' => $total['bienes_servicios'],
                'beneficiarios' => $total['beneficiarios'],
                'aporte' => round($total['aporte'], 2),
                'ods_con_actividad' => count($porOds),
            ],
            'sin_ods' => ['bienes_servicios' => $sinOds['bienes_servicios'], 'aporte' => round($sinOds['aporte'], 2)],
            'ods' => $ods,
        ];
    }

    /**
     * Cada EPS aprobado con lo que registró, su ubicación y su indicador de articulación (para exportar).
     *
     * @param  Filtros  $filtros
     * @return list<array<string, mixed>>
     */
    public function expedientesDetallados(array $filtros): array
    {
        $registros = $this->registros($this->expedientes($filtros));
        $ubicaciones = $this->ubicaciones($this->expedientes($filtros));
        $departamentos = Departamento::query()->whereIn('id', $ubicaciones->pluck('departamento_id'))->get(['id', 'codigo', 'nombre'])->keyBy('id');
        $municipios = Municipio::query()->whereIn('id', $ubicaciones->pluck('municipio_id')->filter())->pluck('nombre', 'id');

        return $this->expedientes($filtros)
            ->with(['estudiante', 'unidadAcademica:id,nombre'])
            ->orderBy('id')
            ->get()
            ->map(function (Expediente $expediente) use ($registros, $ubicaciones, $departamentos, $municipios): array {
                $registro = $registros[$expediente->id];
                $ubicacion = $ubicaciones[$expediente->id] ?? null;
                $departamento = $ubicacion === null ? null : $departamentos[$ubicacion->departamento_id] ?? null;

                return [
                    'id' => $expediente->id,
                    'estudiante' => $expediente->estudiante->nombre_completo,
                    'carrera' => $expediente->nombre_carrera,
                    'unidad' => $expediente->unidadAcademica->nombre,
                    'programa' => $expediente->programa?->etiqueta() ?? 'EPS',
                    'aprobado' => $expediente->verificado_at?->format('d-m-Y'),
                    'departamento_codigo' => $departamento?->codigo,
                    'departamento' => $departamento?->nombre,
                    'municipio' => $ubicacion?->municipio_id === null ? null : $municipios[$ubicacion->municipio_id] ?? null,
                    'bienes_servicios' => $registro['bienes'],
                    'beneficiarios' => $registro['beneficiarios'],
                    'aporte' => round($registro['aporte'], 2),
                    'investigaciones' => $registro['investigaciones'],
                    'acciones' => $registro['acciones'],
                    'participantes' => $registro['participantes'],
                    'instituciones' => count($registro['instituciones']),
                    'articulacion' => Articulacion::desdeConteos($registro['bienes'], $registro['investigaciones'], $registro['acciones'], count($registro['instituciones'])),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Bienes y servicios que incluyeron a cada población vulnerable entre sus beneficiarios.
     *
     * @param  Filtros  $filtros
     * @return list<array{nombre: string, eps: int, registros: int, cantidad: int, beneficiarios: int, aporte: float}>
     */
    public function poblacionesVulnerables(array $filtros): array
    {
        $totales = DB::table('bien_servicio_poblacion as marca')
            ->join('bienes_servicios as bien', 'bien.id', '=', 'marca.bien_servicio_id')
            ->leftJoin('catalogos as elemento', 'elemento.id', '=', 'bien.catalogo_id')
            ->whereNull('bien.deleted_at')
            ->whereIn('bien.expediente_id', $this->expedientes($filtros)->select('id'))
            ->groupBy('marca.catalogo_id')
            ->selectRaw('marca.catalogo_id, count(distinct bien.expediente_id) as eps, count(*) as registros, sum(coalesce(bien.cantidad, 1)) as cantidad, sum(coalesce(bien.cantidad_beneficiarios, 0)) as beneficiarios, sum(coalesce(bien.cantidad, 1) * coalesce(elemento.valor_base, 0)) as aporte')
            ->get()
            ->keyBy('catalogo_id');

        return Catalogo::query()
            ->delCatalogo(TipoCatalogo::PoblacionesVulnerables)
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (Catalogo $poblacion): array => [
                'nombre' => $poblacion->nombre,
                'eps' => (int) ($totales[$poblacion->id]->eps ?? 0),
                'registros' => (int) ($totales[$poblacion->id]->registros ?? 0),
                'cantidad' => (int) ($totales[$poblacion->id]->cantidad ?? 0),
                'beneficiarios' => (int) ($totales[$poblacion->id]->beneficiarios ?? 0),
                'aporte' => round((float) ($totales[$poblacion->id]->aporte ?? 0), 2),
            ])
            ->values()
            ->all();
    }

    /**
     * Acumula cantidad, aporte y beneficiarios en el grupo indicado (un bien, una línea, una unidad…).
     *
     * @param  array<string, array<string, mixed>>  $grupo
     * @param  array<string, mixed>  $datos
     */
    private function sumar(array &$grupo, string $clave, array $datos, int $cantidad, float $aporte, int $beneficiarios = 0): void
    {
        $grupo[$clave] ??= [...$datos, 'cantidad' => 0, 'beneficiarios' => 0, 'aporte' => 0.0];
        $grupo[$clave]['cantidad'] += $cantidad;
        $grupo[$clave]['beneficiarios'] += $beneficiarios;
        $grupo[$clave]['aporte'] += $aporte;
    }

    /**
     * Carreras con EPS, opcionalmente solo las de una unidad.
     *
     * @return Collection<int, string>
     */
    public function carreras(?int $unidad, ?User $alcance = null): Collection
    {
        return $this->validosVisibles(['alcance' => $alcance])
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
        return $this->validosVisibles($filtros)
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
     * EPS válidos para las estadísticas que entran en lo que ve quien consulta.
     *
     * @param  array{alcance?: User|null}  $filtros
     * @return Builder<Expediente>
     */
    private function validosVisibles(array $filtros): Builder
    {
        return Expediente::query()
            ->validos()
            ->when($filtros['alcance'] ?? null, fn (Builder $consulta, User $usuario) => $consulta->alcanceDeConsulta($usuario));
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
            ->selectRaw('expediente_id, catalogo_id, tipo, coalesce(sum(coalesce(cantidad, 1)), 0) as total, coalesce(sum(cantidad_beneficiarios), 0) as beneficiarios')
            ->groupBy('expediente_id', 'catalogo_id', 'tipo')
            ->get()
            ->groupBy('expediente_id');
        $catalogo = Catalogo::query()
            ->whereIn('id', $bienes->flatten()->pluck('catalogo_id')->filter()->unique())
            ->get(['id', 'nombre', 'valor_base'])
            ->keyBy('id');
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
                $elemento = $catalogo[$fila->catalogo_id] ?? null;
                $nombre = $elemento->nombre ?? self::SIN_CLASIFICAR;
                $clave = "{$fila->tipo->value}|{$nombre}";
                $items[$clave] ??= ['nombre' => $nombre, 'tipo' => $fila->tipo->value, 'cantidad' => 0, 'beneficiarios' => 0, 'aporte' => 0.0];
                $items[$clave]['cantidad'] += (int) $fila->total;
                $items[$clave]['beneficiarios'] += (int) $fila->beneficiarios;
                $items[$clave]['aporte'] += (int) $fila->total * (float) ($elemento->valor_base ?? 0);
            }

            return [
                'estudiante' => $expediente->estudiante_id,
                'bienes' => array_sum(array_column($items, 'cantidad')),
                'aporte' => array_sum(array_column($items, 'aporte')),
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
