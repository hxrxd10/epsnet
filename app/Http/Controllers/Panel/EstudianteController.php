<?php

namespace App\Http\Controllers\Panel;

use App\Actions\Expedientes\ArmarDetalleExpediente;
use App\Enums\Eje;
use App\Enums\EstadoExpediente;
use App\Enums\ProgramaEps;
use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class EstudianteController extends Controller
{
    /**
     * Estudiantes y EPS: DIGEU ve todos y cada unidad académica los de su unidad.
     */
    public function index(Request $request): Response
    {
        $usuario = $request->user();

        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', Rule::enum(EstadoExpediente::class)],
            'unidad' => ['nullable', 'integer'],
        ]);

        $expedientes = Expediente::query()
            ->visiblesPara($usuario)
            ->with(['estudiante', 'unidadAcademica'])
            ->withCount(array_map(fn (Eje $eje): string => $eje->relacion(), Eje::cases()))
            ->when($filtros['q'] ?? null, fn ($consulta, string $texto) => $consulta->where(
                fn ($consulta) => $consulta
                    ->where('nombre_carrera', 'like', "%{$texto}%")
                    ->orWhereHas('estudiante', fn ($consulta) => $consulta
                        ->where('carnet', 'like', "%{$texto}%")
                        ->orWhere('nombre1', 'like', "%{$texto}%")
                        ->orWhere('apellido1', 'like', "%{$texto}%")
                        ->orWhere('apellido2', 'like', "%{$texto}%")),
            ))
            ->when($filtros['estado'] ?? null, fn ($consulta, string $estado) => $consulta->where('estado_expediente', $estado))
            ->when($usuario->esAdministrador() && ($filtros['unidad'] ?? null), fn ($consulta) => $consulta->where('unidad_academica_id', $filtros['unidad']))
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('estudiantes/index', [
            'expedientes' => $expedientes->getCollection()->map(fn (Expediente $expediente): array => [
                'id' => $expediente->id,
                'estudiante' => $expediente->estudiante->nombre_completo,
                'carnet' => $expediente->estudiante->carnet,
                'carrera' => $expediente->nombre_carrera,
                'unidad' => $expediente->unidadAcademica->nombre,
                'estado' => $expediente->estado_expediente->value,
                'estado_etiqueta' => $expediente->estado_expediente->etiqueta(),
                'es_epsum' => $expediente->programa === ProgramaEps::Epsum,
                'registros' => collect(Eje::cases())->sum(fn (Eje $eje): int => (int) $expediente->getAttribute(str($eje->relacion())->snake().'_count')),
                'actualizado' => $expediente->updated_at?->toDateString(),
            ])->values(),
            'pagina' => [
                'actual' => $expedientes->currentPage(),
                'ultima' => $expedientes->lastPage(),
                'total' => $expedientes->total(),
                'anterior' => $expedientes->previousPageUrl(),
                'siguiente' => $expedientes->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'estado' => $filtros['estado'] ?? '', 'unidad' => (string) ($filtros['unidad'] ?? '')],
            'estados' => EstadoExpediente::opciones(),
            'unidades' => $usuario->esAdministrador()
                ? UnidadAcademica::orderBy('nombre')->get(['id', 'nombre'])->map(fn (UnidadAcademica $unidad): array => ['value' => (string) $unidad->id, 'label' => $unidad->nombre])->all()
                : [],
            'sinUnidad' => $usuario->esUnidadAcademica() && $usuario->unidadAcademica === null,
            'ambito' => $usuario->esAdministrador() ? 'Todas las unidades académicas' : ($usuario->unidadAcademica?->nombre ?? 'Sin unidad asignada'),
        ]);
    }

    public function show(Request $request, Expediente $expediente, ArmarDetalleExpediente $detalle): Response
    {
        $expediente->load(['estudiante', 'unidadAcademica', 'verificadoPor', 'ordenImpresion']);

        return Inertia::render('estudiantes/show', [
            'expediente' => [
                'id' => $expediente->id,
                'estudiante' => $expediente->estudiante->nombre_completo,
                'carnet' => $expediente->estudiante->carnet,
                'nacionalidad' => $expediente->estudiante->nacionalidad,
                'carrera' => $expediente->nombre_carrera,
                'extension' => $expediente->nombre_extension,
                'nivel' => $expediente->nivel_academico,
                'unidad' => $expediente->unidadAcademica->nombre,
                'estado' => $expediente->estado_expediente->value,
                'estado_etiqueta' => $expediente->estado_expediente->etiqueta(),
                'es_epsum' => $expediente->programa === ProgramaEps::Epsum,
                'completado_at' => $expediente->completado_at?->toIso8601String(),
                'verificado_at' => $expediente->verificado_at?->toIso8601String(),
                'verificado_por' => $expediente->verificadoPor?->name,
                'solicitud_at' => $expediente->estado_expediente === EstadoExpediente::Verificado ? null : $expediente->aprobacion_solicitada_at?->toIso8601String(),
            ],
            'desdeBandeja' => $request->query('desde') === 'bandeja',
            'orden_impresion' => $expediente->ordenImpresion === null ? null : [
                'nombre' => $expediente->ordenImpresion->nombre_original,
                'tamano_bytes' => $expediente->ordenImpresion->tamano_bytes,
                'descarga' => route('panel.estudiantes.orden-impresion', $expediente, absolute: false),
            ],
            'ejes' => $detalle->handle($expediente),
            'puedeVerificar' => $request->user()->can('verificar', $expediente),
        ]);
    }
}
