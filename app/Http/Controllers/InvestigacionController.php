<?php

namespace App\Http\Controllers;

use App\Enums\EstadoExpediente;
use App\Enums\TipoPublicacion;
use App\Models\Expediente;
use App\Models\PublicacionInvestigacion;
use App\Models\UnidadAcademica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class InvestigacionController extends Controller
{
    /**
     * Investigaciones: como el repositorio, pero solo las publicaciones de investigación de los EPS
     * aprobados por supervisión (de su unidad y sus carreras, para una unidad académica).
     */
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'unidad' => ['nullable', 'integer'],
            'tipo' => ['nullable', Rule::enum(TipoPublicacion::class)],
        ]);

        $investigaciones = PublicacionInvestigacion::query()
            ->whereHas('expediente', fn (Builder $consulta) => $consulta
                ->where('estado_expediente', EstadoExpediente::Verificado)
                ->alcanceDeConsulta($request->user())
                ->when($filtros['unidad'] ?? null, fn (Builder $consulta, int|string $unidad) => $consulta->where('unidad_academica_id', $unidad)))
            ->with(['expediente.estudiante', 'expediente.unidadAcademica'])
            ->when($filtros['q'] ?? null, fn (Builder $consulta, string $texto) => $consulta->where(
                fn (Builder $consulta) => $consulta
                    ->where('titulo', 'like', "%{$texto}%")
                    ->orWhere('autores', 'like', "%{$texto}%")
                    ->orWhere('resumen', 'like', "%{$texto}%")
                    ->orWhereHas('expediente', fn (Builder $consulta) => $consulta->where('nombre_carrera', 'like', "%{$texto}%")),
            ))
            ->when($filtros['tipo'] ?? null, fn (Builder $consulta, string $tipo) => $consulta->where('tipo', $tipo))
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('investigaciones/index', [
            'investigaciones' => $investigaciones->getCollection()->map(fn (PublicacionInvestigacion $investigacion): array => [
                'id' => $investigacion->id,
                'titulo' => Str::limit(Str::squish($investigacion->titulo), 300),
                'tipo' => $investigacion->tipo->etiqueta(),
                'autores' => Str::limit(Str::squish($investigacion->autores), 200),
                'medio' => $investigacion->medio_publicacion,
                'resumen' => $investigacion->resumen === null ? null : Str::limit(Str::squish($investigacion->resumen), 400),
                'enlace' => $investigacion->enlace,
                'fecha' => $investigacion->fecha_publicacion?->toDateString(),
                'carrera' => $investigacion->expediente->nombre_carrera,
                'unidad' => $investigacion->expediente->unidadAcademica->nombre,
                'estudiante' => $investigacion->expediente->estudiante->nombre_completo,
                'expediente_id' => $investigacion->expediente_id,
            ])->values(),
            'pagina' => [
                'actual' => $investigaciones->currentPage(),
                'ultima' => $investigaciones->lastPage(),
                'total' => $investigaciones->total(),
                'anterior' => $investigaciones->previousPageUrl(),
                'siguiente' => $investigaciones->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'unidad' => (string) ($filtros['unidad'] ?? ''), 'tipo' => $filtros['tipo'] ?? ''],
            'unidades' => UnidadAcademica::query()
                ->whereIn('id', Expediente::query()->where('estado_expediente', EstadoExpediente::Verificado)->alcanceDeConsulta($request->user())->select('unidad_academica_id'))
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn (UnidadAcademica $unidad): array => ['value' => (string) $unidad->id, 'label' => $unidad->nombre])
                ->all(),
            'tipos' => TipoPublicacion::opciones(),
        ]);
    }
}
