<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartamentoRequest;
use App\Models\Departamento;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DepartamentoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/departamentos', [
            'departamentos' => Departamento::query()
                ->withCount('municipios')
                ->orderBy('codigo')
                ->get()
                ->map(fn (Departamento $departamento): array => [
                    'id' => $departamento->id,
                    'codigo' => $departamento->codigo,
                    'nombre' => $departamento->nombre,
                    'cabecera' => $departamento->cabecera,
                    'latitud' => $departamento->latitud,
                    'longitud' => $departamento->longitud,
                    'municipios' => $departamento->municipios_count,
                ])
                ->values(),
            'googleMaps' => $this->googleMaps(),
        ]);
    }

    public function store(DepartamentoRequest $request): RedirectResponse
    {
        Departamento::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Departamento agregado.']);

        return to_route('admin.departamentos.index');
    }

    public function update(DepartamentoRequest $request, Departamento $departamento): RedirectResponse
    {
        $departamento->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Departamento actualizado.']);

        return to_route('admin.departamentos.index');
    }

    /**
     * Un departamento con municipios o con EPS ubicados en él no se elimina.
     */
    public function destroy(Departamento $departamento): RedirectResponse
    {
        if ($departamento->estaEnUso()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Este departamento tiene municipios o EPS registrados; no se puede eliminar.']);

            return to_route('admin.departamentos.index');
        }

        $departamento->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Departamento eliminado.']);

        return to_route('admin.departamentos.index');
    }
}
