<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoCatalogo;
use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use Inertia\Inertia;
use Inertia\Response;

class ManejoDatosController extends Controller
{
    /**
     * Portada de "Manejo de datos": los catálogos que los administradores pueden mantener.
     */
    public function index(): Response
    {
        $conteos = Catalogo::query()
            ->selectRaw('catalogo, count(*) as total, sum(case when activo then 1 else 0 end) as activos')
            ->groupBy('catalogo')
            ->get()
            ->keyBy(fn (Catalogo $fila): string => $fila->catalogo->value);

        return Inertia::render('admin/datos', [
            'catalogos' => collect(TipoCatalogo::cases())->map(fn (TipoCatalogo $catalogo): array => [
                'clave' => $catalogo->value,
                'etiqueta' => $catalogo->etiqueta(),
                'descripcion' => $catalogo->descripcion(),
                'total' => (int) ($conteos[$catalogo->value]->total ?? 0),
                'activos' => (int) ($conteos[$catalogo->value]->activos ?? 0),
                'href' => route('admin.catalogos.index', $catalogo, absolute: false),
            ])->values(),
        ]);
    }
}
