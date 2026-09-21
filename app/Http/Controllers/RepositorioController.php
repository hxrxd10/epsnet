<?php

namespace App\Http\Controllers;

use App\Actions\Expedientes\ArmarDetalleExpediente;
use App\Enums\Eje;
use App\Enums\EstadoExpediente;
use App\Enums\ProgramaEps;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RepositorioController extends Controller
{
    /**
     * Repositorio: los EPS aprobados por su unidad académica, con una descripción de lo realizado.
     */
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'unidad' => ['nullable', 'integer'],
        ]);

        $expedientes = Expediente::query()
            ->where('estado_expediente', EstadoExpediente::Verificado)
            ->with([
                'estudiante',
                'unidadAcademica',
                'ubicaciones.departamento',
                'ubicaciones.municipio',
                'bienesServicios:id,expediente_id,descripcion,fecha',
                'transferencias:id,expediente_id,actividad,fecha',
                'seguimientos:id,expediente_id,evaluacion_impacto,fecha',
            ])
            ->withCount(array_map(fn (Eje $eje): string => $eje->relacion(), Eje::cases()))
            ->when($filtros['q'] ?? null, fn ($consulta, string $texto) => $consulta->where(
                fn ($consulta) => $consulta
                    ->where('nombre_carrera', 'like', "%{$texto}%")
                    ->orWhereHas('estudiante', fn ($consulta) => $consulta
                        ->where('nombre1', 'like', "%{$texto}%")
                        ->orWhere('apellido1', 'like', "%{$texto}%")
                        ->orWhere('apellido2', 'like', "%{$texto}%")),
            ))
            ->when($filtros['unidad'] ?? null, fn ($consulta, int|string $unidad) => $consulta->where('unidad_academica_id', $unidad))
            ->orderByDesc('verificado_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('repositorio/index', [
            'expedientes' => $expedientes->getCollection()->map(fn (Expediente $expediente): array => [
                'id' => $expediente->id,
                'estudiante' => $expediente->estudiante->nombre_completo,
                'carrera' => $expediente->nombre_carrera,
                'unidad' => $expediente->unidadAcademica->nombre,
                'es_epsum' => $expediente->programa === ProgramaEps::Epsum,
                'ubicacion' => $expediente->ubicaciones->map(fn ($ubicacion): string => collect([$ubicacion->municipio?->nombre, $ubicacion->departamento->nombre])->filter()->implode(', '))->unique()->take(2)->implode(' · '),
                'descripcion' => $this->descripcion($expediente),
                'bienes_servicios' => $expediente->bienes_servicios_count,
                'acciones' => $expediente->transferencias_count,
                'publicaciones' => $expediente->publicaciones_count,
                'aprobado' => $expediente->verificado_at?->toDateString(),
            ])->values(),
            'pagina' => [
                'actual' => $expedientes->currentPage(),
                'ultima' => $expedientes->lastPage(),
                'total' => $expedientes->total(),
                'anterior' => $expedientes->previousPageUrl(),
                'siguiente' => $expedientes->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'unidad' => (string) ($filtros['unidad'] ?? '')],
            'unidades' => UnidadAcademica::orderBy('nombre')->get(['id', 'nombre'])->map(fn (UnidadAcademica $unidad): array => ['value' => (string) $unidad->id, 'label' => $unidad->nombre])->all(),
        ]);
    }

    public function show(Expediente $expediente, ArmarDetalleExpediente $detalle): Response
    {
        abort_unless($expediente->estado_expediente === EstadoExpediente::Verificado, 404);

        $expediente->load(['estudiante', 'unidadAcademica']);

        return Inertia::render('repositorio/show', [
            'expediente' => [
                'id' => $expediente->id,
                'estudiante' => $expediente->estudiante->nombre_completo,
                'carrera' => $expediente->nombre_carrera,
                'unidad' => $expediente->unidadAcademica->nombre,
                'aprobado' => $expediente->verificado_at?->toDateString(),
            ],
            'ejes' => $detalle->handle($expediente),
        ]);
    }

    /**
     * Resumen de lo realizado: la evaluación de impacto si existe y, si no, lo primero que se registró.
     */
    private function descripcion(Expediente $expediente): ?string
    {
        $texto = $expediente->seguimientos->sortByDesc('fecha')->pluck('evaluacion_impacto')->filter()->first()
            ?? $expediente->bienesServicios->sortBy('fecha')->pluck('descripcion')->first()
            ?? $expediente->transferencias->sortBy('fecha')->pluck('actividad')->first();

        return $texto === null ? null : Str::limit(Str::squish($texto), 280);
    }
}
