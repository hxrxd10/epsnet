<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InstitucionAliadaRequest;
use App\Models\InstitucionAliada;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InstitucionAliadaController extends Controller
{
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'string', 'max:100'],
        ]);

        $instituciones = InstitucionAliada::query()
            ->withCount('alianzas')
            ->when($filtros['q'] ?? null, fn (Builder $consulta, string $texto) => $consulta->where(
                fn (Builder $consulta) => $consulta
                    ->where('nombre', 'like', "%{$texto}%")
                    ->orWhere('nombre_contacto', 'like', "%{$texto}%")
                    ->orWhere('correo_contacto', 'like', "%{$texto}%"),
            ))
            ->when($filtros['tipo'] ?? null, fn (Builder $consulta, string $tipo) => $consulta->where('tipo', $tipo))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/instituciones', [
            'instituciones' => $instituciones->getCollection()->map(fn (InstitucionAliada $institucion): array => [
                'id' => $institucion->id,
                'nombre' => $institucion->nombre,
                'tipo' => $institucion->tipo,
                'nombre_contacto' => $institucion->nombre_contacto,
                'correo_contacto' => $institucion->correo_contacto,
                'telefono_contacto' => $institucion->telefono_contacto,
                'direccion' => $institucion->direccion,
                'eps' => $institucion->alianzas_count,
            ])->values(),
            'pagina' => [
                'actual' => $instituciones->currentPage(),
                'ultima' => $instituciones->lastPage(),
                'total' => $instituciones->total(),
                'anterior' => $instituciones->previousPageUrl(),
                'siguiente' => $instituciones->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'tipo' => $filtros['tipo'] ?? ''],
            'tipos' => InstitucionAliada::query()->whereNotNull('tipo')->distinct()->orderBy('tipo')->pluck('tipo'),
        ]);
    }

    public function store(InstitucionAliadaRequest $request): RedirectResponse
    {
        InstitucionAliada::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Institución aliada agregada.']);

        return back();
    }

    public function update(InstitucionAliadaRequest $request, InstitucionAliada $institucion): RedirectResponse
    {
        $institucion->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Institución aliada actualizada.']);

        return back();
    }

    /**
     * Una institución aliada que figura en algún EPS no se elimina; una sin uso se borra del todo para liberar su nombre.
     */
    public function destroy(InstitucionAliada $institucion): RedirectResponse
    {
        if ($institucion->estaEnUso()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Esta institución aliada figura en el EPS de algún estudiante; no se puede eliminar.']);

            return back();
        }

        $institucion->forceDelete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Institución aliada eliminada.']);

        return back();
    }
}
