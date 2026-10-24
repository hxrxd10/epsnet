<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoCatalogo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CatalogoRequest;
use App\Models\Catalogo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CatalogoController extends Controller
{
    public function index(TipoCatalogo $catalogo): Response
    {
        $categorias = collect($catalogo->categorias())->pluck('label', 'value');
        $vinculos = $catalogo->vinculos();
        $anclados = $this->anclados($catalogo);

        return Inertia::render('admin/catalogo', [
            'catalogo' => [
                'clave' => $catalogo->value,
                'etiqueta' => $catalogo->etiqueta(),
                'singular' => $catalogo->singular(),
                'descripcion' => $catalogo->descripcion(),
                'usa_categoria' => $catalogo->usaCategoria(),
                'usa_valor_base' => $catalogo->usaValorBase(),
                'categorias' => $catalogo->categorias(),
                'vinculos' => collect($vinculos)->map(fn (TipoCatalogo $origen, string $campo): array => [
                    'campo' => $campo,
                    'etiqueta' => $origen->singular(),
                    'opciones' => $this->ordenados(Catalogo::query()->delCatalogo($origen)->get(['id', 'nombre', 'activo']))
                        ->map(fn (Catalogo $opcion): array => [
                            'value' => (string) $opcion->id,
                            'label' => $opcion->nombre.($opcion->activo ? '' : ' (inactivo)'),
                        ])
                        ->values()
                        ->all(),
                ])->values()->all(),
            ],
            'elementos' => $this->ordenados(Catalogo::query()
                ->delCatalogo($catalogo)
                ->with(array_map(fn (string $campo): string => Str::camel(Str::beforeLast($campo, '_id')).':id,nombre', array_keys($vinculos)))
                ->withExists([
                    'bienesServicios as usado_en_bienes' => fn ($consulta) => $consulta->withTrashed(),
                    'transferencias as usado_en_transferencias' => fn ($consulta) => $consulta->withTrashed(),
                ])
                ->get())
                ->map(fn (Catalogo $elemento): array => [
                    'id' => $elemento->id,
                    'nombre' => $elemento->nombre,
                    'descripcion' => $elemento->descripcion,
                    'categoria' => $elemento->categoria,
                    'categoria_etiqueta' => $categorias[$elemento->categoria] ?? null,
                    'valor_base' => $elemento->valor_base,
                    'vinculos' => collect($vinculos)->map(fn (TipoCatalogo $origen, string $campo): array => [
                        'id' => $elemento->{$campo},
                        'nombre' => $elemento->{Str::camel(Str::beforeLast($campo, '_id'))}?->nombre,
                    ])->all(),
                    'anclados' => $anclados[$elemento->id] ?? 0,
                    'activo' => $elemento->activo,
                    'en_uso' => (bool) ($elemento->usado_en_bienes || $elemento->usado_en_transferencias) || ($anclados[$elemento->id] ?? 0) > 0,
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

    /**
     * Cuántos bienes y servicios están anclados a cada elemento de una línea, política, área u ODS.
     *
     * @return array<int, int>
     */
    private function anclados(TipoCatalogo $catalogo): array
    {
        $campo = array_search($catalogo, TipoCatalogo::BienesServicios->vinculos(), true);

        if ($campo === false) {
            return [];
        }

        return Catalogo::query()
            ->delCatalogo(TipoCatalogo::BienesServicios)
            ->whereNotNull($campo)
            ->select($campo)
            ->selectRaw('count(*) as total')
            ->groupBy($campo)
            ->pluck('total', $campo)
            ->map(fn (int|string $total): int => (int) $total)
            ->all();
    }

    /**
     * Orden natural por nombre, para que «ODS 2» quede antes que «ODS 10».
     *
     * @param  Collection<int, Catalogo>  $elementos
     * @return Collection<int, Catalogo>
     */
    private function ordenados(Collection $elementos): Collection
    {
        return $elementos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)->values();
    }
}
