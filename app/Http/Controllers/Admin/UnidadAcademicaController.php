<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoUnidadAcademica;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnidadAcademicaRequest;
use App\Models\UnidadAcademica;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UnidadAcademicaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/unidades', [
            'unidades' => UnidadAcademica::query()
                ->with('administrador:id,name')
                ->withCount('expedientes')
                ->orderBy('nombre')
                ->get()
                ->map(fn (UnidadAcademica $unidad): array => [
                    'id' => $unidad->id,
                    'nombre' => $unidad->nombre,
                    'siglas' => $unidad->siglas,
                    'tipo' => $unidad->tipo->value,
                    'tipo_etiqueta' => $unidad->tipo->etiqueta(),
                    'nombre_contacto' => $unidad->nombre_contacto,
                    'correo_contacto' => $unidad->correo_contacto,
                    'telefono_contacto' => $unidad->telefono_contacto,
                    'direccion' => $unidad->direccion,
                    'latitud' => $unidad->latitud,
                    'longitud' => $unidad->longitud,
                    'activa' => $unidad->activa,
                    'administrador' => $unidad->administrador?->name,
                    'expedientes' => $unidad->expedientes_count,
                ])
                ->values(),
            'tipos' => TipoUnidadAcademica::opciones(),
            'googleMaps' => $this->googleMaps(),
        ]);
    }

    public function store(UnidadAcademicaRequest $request): RedirectResponse
    {
        UnidadAcademica::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Unidad académica agregada.']);

        return to_route('admin.unidades.index');
    }

    public function update(UnidadAcademicaRequest $request, UnidadAcademica $unidad): RedirectResponse
    {
        $unidad->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Unidad académica actualizada.']);

        return to_route('admin.unidades.index');
    }

    /**
     * Una unidad con EPS o con un administrador asignado no se elimina: se desactiva.
     */
    public function destroy(UnidadAcademica $unidad): RedirectResponse
    {
        if ($unidad->expedientes()->exists() || $unidad->administrador_id !== null) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Esta unidad tiene EPS o un administrador asignado. Desactívala en lugar de eliminarla.']);

            return to_route('admin.unidades.index');
        }

        $unidad->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Unidad académica eliminada.']);

        return to_route('admin.unidades.index');
    }
}
