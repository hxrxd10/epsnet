<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoCatalogo;
use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use App\Models\Departamento;
use App\Models\InstitucionAliada;
use App\Models\Municipio;
use App\Models\UnidadAcademica;
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
            'territorio' => [
                [
                    'clave' => 'departamentos',
                    'etiqueta' => 'Departamentos',
                    'descripcion' => 'Los departamentos del país, con su cabecera y una coordenada de referencia.',
                    'total' => Departamento::count(),
                    'activos' => null,
                    'href' => route('admin.departamentos.index', absolute: false),
                ],
                [
                    'clave' => 'municipios',
                    'etiqueta' => 'Municipios',
                    'descripcion' => 'Los municipios de cada departamento. Los estudiantes eligen su municipio de este catálogo.',
                    'total' => Municipio::count(),
                    'activos' => null,
                    'href' => route('admin.municipios.index', absolute: false),
                ],
                [
                    'clave' => 'instituciones',
                    'etiqueta' => 'Instituciones aliadas',
                    'descripcion' => 'Ministerios, ONG y socios que participaron o cooperaron con los proyectos. Se eligen de este catálogo y se cuantifican en las estadísticas.',
                    'total' => InstitucionAliada::count(),
                    'activos' => null,
                    'href' => route('admin.instituciones.index', absolute: false),
                ],
                [
                    'clave' => 'unidades',
                    'etiqueta' => 'Unidades académicas',
                    'descripcion' => 'Facultades, escuelas y centros universitarios, con la ubicación de su edificio.',
                    'total' => UnidadAcademica::count(),
                    'activos' => UnidadAcademica::where('activa', true)->count(),
                    'href' => route('admin.unidades.index', absolute: false),
                ],
            ],
        ]);
    }
}
