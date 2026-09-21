<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoCatalogo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CatalogoRequest;
use App\Models\Catalogo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CatalogoController extends Controller
{
    public function index(TipoCatalogo $catalogo): Response
    {
        $categorias = collect($catalogo->categorias())->pluck('label', 'value');

        return Inertia::render('admin/catalogo', [
            'catalogo' => [
                'clave' => $catalogo->value,
                'etiqueta' => $catalogo->etiqueta(),
                'descripcion' => $catalogo->descripcion(),
                'usa_categoria' => $catalogo->usaCategoria(),
                'categorias' => $catalogo->categorias(),
            ],
            'elementos' => Catalogo::query()
                ->delCatalogo($catalogo)
                ->withExists([
                    'bienesServicios as usado_en_bienes' => fn ($consulta) => $consulta->withTrashed(),
                    'transferencias as usado_en_transferencias' => fn ($consulta) => $consulta->withTrashed(),
                ])
                ->orderBy('nombre')
                ->get()
                ->map(fn (Catalogo $elemento): array => [
                    'id' => $elemento->id,
                    'nombre' => $elemento->nombre,
                    'descripcion' => $elemento->descripcion,
                    'categoria' => $elemento->categoria,
                    'categoria_etiqueta' => $categorias[$elemento->categoria] ?? null,
                    'activo' => $elemento->activo,
                    'en_uso' => (bool) ($elemento->usado_en_bienes || $elemento->usado_en_transferencias),
                ])
                ->values(),
        ]);
    }

    public function store(CatalogoRequest $request, TipoCatalogo $catalogo): RedirectResponse
    {
        Catalogo::create([...$request->validated(), 'catalogo' => $catalogo]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Elemento agregado al catálogo.']);

        return to_route('admin.catalogos.index', $catalogo);
    }

    public function update(CatalogoRequest $request, TipoCatalogo $catalogo, int $item): RedirectResponse
    {
        Catalogo::query()->delCatalogo($catalogo)->findOrFail($item)->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Elemento actualizado.']);

        return to_route('admin.catalogos.index', $catalogo);
    }

    /**
     * Un elemento que ya usan los estudiantes no se elimina: se desactiva para conservar sus registros.
     */
    public function destroy(TipoCatalogo $catalogo, int $item): RedirectResponse
    {
        $elemento = Catalogo::query()->delCatalogo($catalogo)->findOrFail($item);

        if ($elemento->estaEnUso()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Este elemento ya lo usan los estudiantes. Desactívalo en lugar de eliminarlo.']);

            return to_route('admin.catalogos.index', $catalogo);
        }

        $elemento->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Elemento eliminado.']);

        return to_route('admin.catalogos.index', $catalogo);
    }
}
