<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MunicipioRequest;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MunicipioController extends Controller
{
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'departamento' => ['nullable', 'integer'],
        ]);

        $municipios = Municipio::query()
            ->with('departamento:id,nombre')
            ->withExists(['ubicaciones as en_uso' => fn (Builder $consulta) => $consulta->withTrashed()])
            ->when($filtros['q'] ?? null, fn (Builder $consulta, string $texto) => $consulta->where(
                fn (Builder $consulta) => $consulta->where('nombre', 'like', "%{$texto}%")->orWhere('codigo', 'like', "%{$texto}%"),
            ))
            ->when($filtros['departamento'] ?? null, fn (Builder $consulta, int|string $departamento) => $consulta->where('departamento_id', $departamento))
            ->orderBy('codigo')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/municipios', [
            'municipios' => $municipios->getCollection()->map(fn (Municipio $municipio): array => [
                'id' => $municipio->id,
                'codigo' => $municipio->codigo,
                'nombre' => $municipio->nombre,
                'departamento_id' => $municipio->departamento_id,
                'departamento' => $municipio->departamento->nombre,
                'latitud' => $municipio->latitud,
                'longitud' => $municipio->longitud,
                'en_uso' => (bool) $municipio->en_uso,
            ])->values(),
            'pagina' => [
                'actual' => $municipios->currentPage(),
                'ultima' => $municipios->lastPage(),
                'total' => $municipios->total(),
                'anterior' => $municipios->previousPageUrl(),
                'siguiente' => $municipios->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'departamento' => (string) ($filtros['departamento'] ?? '')],
            'departamentos' => Departamento::orderBy('codigo')->get(['id', 'codigo', 'nombre'])->map(fn (Departamento $departamento): array => [
                'value' => (string) $departamento->id,
                'label' => $departamento->nombre,
                'codigo' => $departamento->codigo,
            ])->values(),
            'googleMaps' => $this->googleMaps(),
        ]);
    }

    public function store(MunicipioRequest $request): RedirectResponse
    {
        Municipio::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Municipio agregado.']);

        return back();
    }

    public function update(MunicipioRequest $request, Municipio $municipio): RedirectResponse
    {
        $municipio->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Municipio actualizado.']);

        return back();
    }

    /**
     * Un municipio donde ya hay EPS registrados no se elimina.
     */
    public function destroy(Municipio $municipio): RedirectResponse
    {
        if ($municipio->estaEnUso()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Hay EPS registrados en este municipio; no se puede eliminar.']);

            return back();
        }

        $municipio->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Municipio eliminado.']);

        return back();
    }
}
