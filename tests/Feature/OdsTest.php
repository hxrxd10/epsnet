<?php

use App\Enums\EstadoExpediente;
use App\Enums\TipoCatalogo;
use App\Models\Catalogo;
use App\Models\Departamento;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * Bien o servicio del catálogo anclado al ODS indicado, con su línea y su valor base.
 */
function bienDelOds(string $ods, string $nombre, float $valor, string $linea = 'Agua y saneamiento'): Catalogo
{
    return Catalogo::factory()->create([
        'nombre' => $nombre,
        'valor_base' => $valor,
        'ods_id' => Catalogo::firstOrCreate(['catalogo' => TipoCatalogo::Ods->value, 'nombre' => $ods])->id,
        'linea_intervencion_id' => Catalogo::firstOrCreate(['catalogo' => TipoCatalogo::LineasIntervencion->value, 'nombre' => $linea])->id,
    ]);
}

/**
 * @param  array<string, mixed>  $atributos
 */
function epsConBienes(array $bienes, array $atributos = []): Expediente
{
    $eps = Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Verificado, 'verificado_at' => '2026-05-01', ...$atributos]);

    foreach ($bienes as [$catalogo, $cantidad, $beneficiarios]) {
        $eps->bienesServicios()->create(['tipo' => 'servicio', 'catalogo_id' => $catalogo->id, 'cantidad' => $cantidad, 'cantidad_beneficiarios' => $beneficiarios, 'descripcion' => 'Registro', 'fecha' => '2026-04-01']);
    }

    return $eps;
}

it('envía a quienes no tienen sesión a iniciar sesión', function () {
    $this->get(route('ods.index'))->assertRedirect(route('login'));
});

it('suma por ODS el aporte, los bienes y servicios, los beneficiarios y los EPS aprobados', function () {
    $agua = bienDelOds('ODS 6: Agua limpia y saneamiento', 'Filtros de agua', 250);
    $taller = bienDelOds('ODS 6: Agua limpia y saneamiento', 'Taller de agua', 100);
    $salud = bienDelOds('ODS 3: Salud y bienestar', 'Jornada de salud', 1000, 'Atención primaria');
    Catalogo::factory()->del(TipoCatalogo::Ods)->create(['nombre' => 'ODS 13: Acción por el clima']);
    $departamento = Departamento::factory()->create(['codigo' => '17']);

    epsConBienes([[$agua, 4, 40], [$salud, 1, 100]])->ubicaciones()->create(['departamento_id' => $departamento->id]);
    epsConBienes([[$agua, 2, 10], [$taller, null, 5]]);
    epsConBienes([[$agua, 50, 500]], ['estado_expediente' => EstadoExpediente::Completo]);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('ods.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ods/index')
            ->where('totales.eps', 2)
            ->where('totales.bienes_servicios', 8)
            ->where('totales.beneficiarios', 155)
            ->where('totales.aporte', 2600)
            ->where('totales.ods_con_actividad', 2)
            ->where('ods', fn ($ods) => collect($ods)->pluck('numero')->all() === [3, 6, 13])
            ->where('ods.1.nombre', 'Agua limpia y saneamiento')
            ->where('ods.1.eps', 2)
            ->where('ods.1.bienes_servicios', 7)
            ->where('ods.1.beneficiarios', 55)
            ->where('ods.1.aporte', 1600)
            ->where('ods.1.departamentos', 1)
            ->where('ods.1.items.0.nombre', 'Filtros de agua')
            ->where('ods.1.items.0.cantidad', 6)
            ->where('ods.1.items.0.aporte', 1500)
            ->where('ods.1.lineas.0.nombre', 'Agua y saneamiento')
            ->where('ods.1.lineas.0.beneficiarios', 55)
            ->where('ods.0.aporte', 1000)
            ->where('ods.2.eps', 0)
            ->where('ods.2.aporte', 0));
});

it('aparta los bienes y servicios que no están anclados a un ODS', function () {
    epsConBienes([[Catalogo::factory()->create(['valor_base' => 30]), 3, 0]]);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('ods.index'))->assertInertia(fn (Assert $page) => $page
        ->where('sin_ods.bienes_servicios', 3)
        ->where('sin_ods.aporte', 90)
        ->where('totales.ods_con_actividad', 0));
});

it('aplica los filtros de las estadísticas', function () {
    $agua = bienDelOds('ODS 6: Agua limpia y saneamiento', 'Filtros de agua', 100);
    $unidad = UnidadAcademica::factory()->create();
    epsConBienes([[$agua, 1, 0]], ['unidad_academica_id' => $unidad->id]);
    epsConBienes([[$agua, 5, 0]]);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('ods.index', ['unidad' => $unidad->id]))
        ->assertInertia(fn (Assert $page) => $page->where('totales.bienes_servicios', 1)->where('filtros.unidad', (string) $unidad->id));
});
