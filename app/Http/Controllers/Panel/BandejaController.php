<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Eje;
use App\Enums\ProgramaEps;
use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BandejaController extends Controller
{
    private const int POR_PAGINA = 15;

    /**
     * Bandeja de solicitudes: los EPS que los estudiantes enviaron a aprobación, del que llegó primero al último.
     * DIGEU ve las de todas las unidades y cada unidad, las suyas.
     */
    public function index(Request $request): Response
    {
        $usuario = $request->user();

        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'unidad' => ['nullable', 'integer'],
        ]);

        $solicitudes = Expediente::query()
            ->visiblesPara($usuario)
            ->pendientesDeAprobacion()
            ->with(['estudiante', 'unidadAcademica'])
            ->withCount(array_map(fn (Eje $eje): string => $eje->relacion(), Eje::cases()))
            ->when($filtros['q'] ?? null, fn (Builder $consulta, string $texto) => $consulta->where(
                fn (Builder $consulta) => $consulta
                    ->where('nombre_carrera', 'like', "%{$texto}%")
                    ->orWhereHas('estudiante', fn (Builder $consulta) => $consulta
                        ->where('carnet', 'like', "%{$texto}%")
                        ->orWhere('nombre1', 'like', "%{$texto}%")
                        ->orWhere('apellido1', 'like', "%{$texto}%")
                        ->orWhere('apellido2', 'like', "%{$texto}%")),
            ))
            ->when($usuario->esAdministrador() && ($filtros['unidad'] ?? null), fn (Builder $consulta) => $consulta->where('unidad_academica_id', $filtros['unidad']))
            ->orderBy('aprobacion_solicitada_at')
            ->orderBy('id')
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        $primera = ($solicitudes->currentPage() - 1) * self::POR_PAGINA;

        return Inertia::render('panel/bandeja', [
            'solicitudes' => $solicitudes->getCollection()->values()->map(fn (Expediente $expediente, int $indice): array => [
                'id' => $expediente->id,
                'posicion' => $primera + $indice + 1,
                'estudiante' => $expediente->estudiante->nombre_completo,
                'carnet' => $expediente->estudiante->carnet,
                'carrera' => $expediente->nombre_carrera,
                'unidad' => $expediente->unidadAcademica->nombre,
                'es_epsum' => $expediente->programa === ProgramaEps::Epsum,
                'registros' => collect(Eje::cases())->sum(fn (Eje $eje): int => (int) $expediente->getAttribute(str($eje->relacion())->snake().'_count')),
                'enviado_at' => $expediente->aprobacion_solicitada_at->toIso8601String(),
                'dias_espera' => (int) floor($expediente->aprobacion_solicitada_at->diffInDays(now(), true)),
            ]),
            'pagina' => [
                'actual' => $solicitudes->currentPage(),
                'ultima' => $solicitudes->lastPage(),
                'total' => $solicitudes->total(),
                'anterior' => $solicitudes->previousPageUrl(),
                'siguiente' => $solicitudes->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'unidad' => (string) ($filtros['unidad'] ?? '')],
            'unidades' => $usuario->esAdministrador()
                ? UnidadAcademica::orderBy('nombre')->get(['id', 'nombre'])->map(fn (UnidadAcademica $unidad): array => ['value' => (string) $unidad->id, 'label' => $unidad->nombre])->all()
                : [],
            'sinUnidad' => $usuario->esUnidadAcademica() && $usuario->unidadAcademica === null,
            'ambito' => $usuario->esAdministrador() ? 'Todas las unidades académicas' : ($usuario->unidadAcademica?->nombre ?? 'Sin unidad asignada'),
        ]);
    }
}
