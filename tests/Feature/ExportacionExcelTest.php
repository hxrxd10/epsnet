<?php

use App\Enums\EstadoExpediente;
use App\Enums\TipoCambioBitacora;
use App\Enums\TipoCatalogo;
use App\Models\Bitacora;
use App\Models\Catalogo;
use App\Models\Departamento;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use App\Models\Municipio;
use App\Models\UnidadAcademica;
use App\Models\User;
use Tests\Support\LectorExcel;

/**
 * EPS aprobado en Petén con un bien o servicio (con ODS y población vulnerable), una publicación y una alianza.
 *
 * @param  array<string, mixed>  $atributos
 */
function epsParaExportar(array $atributos = []): Expediente
{
    $peten = Departamento::firstOrCreate(['codigo' => '17'], ['nombre' => 'Petén', 'cabecera' => 'Flores']);
    $flores = Municipio::firstOrCreate(['departamento_id' => $peten->id, 'nombre' => 'Flores'], ['codigo' => '1701']);
    $bien = Catalogo::where('nombre', 'Filtros de agua')->first() ?? Catalogo::factory()->create([
        'nombre' => 'Filtros de agua',
        'valor_base' => 250,
        'ods_id' => Catalogo::firstOrCreate(['catalogo' => TipoCatalogo::Ods->value, 'nombre' => 'ODS 6: Agua limpia y saneamiento'])->id,
    ]);
    $mujeres = Catalogo::firstOrCreate(['catalogo' => TipoCatalogo::PoblacionesVulnerables->value, 'nombre' => 'Mujeres']);

    $eps = Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Verificado, 'verificado_at' => '2026-05-01', 'nombre_carrera' => 'Ingeniería Civil', ...$atributos]);
    $eps->ubicaciones()->create(['departamento_id' => $peten->id, 'municipio_id' => $flores->id]);
    $eps->bienesServicios()->create(['tipo' => 'bien', 'catalogo_id' => $bien->id, 'cantidad' => 4, 'cantidad_beneficiarios' => 30, 'descripcion' => 'Filtros', 'fecha' => '2026-04-01'])
        ->poblaciones()->attach($mujeres);
    $eps->publicaciones()->create(['titulo' => 'Agua en Petén', 'tipo' => 'articulo', 'autores' => 'A. Pérez', 'fecha_publicacion' => '2026-03-15']);
    $eps->alianzas()->create(['institucion_aliada_id' => InstitucionAliada::factory()->create()->id]);

    return $eps;
}

it('exige iniciar sesión para exportar', function (string $ruta, array $parametros) {
    $this->get(route($ruta, $parametros))->assertRedirect(route('login'));
})->with([
    'país' => ['estadisticas.excel', []],
    'departamento' => ['estadisticas.departamento.excel', ['departamento' => '17']],
    'ODS' => ['ods.excel', []],
]);

it('exporta las estadísticas del país con los EPS, su articulación y las poblaciones vulnerables', function () {
    $eps = epsParaExportar();
    Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Completo]);
    $usuario = User::factory()->administrador()->create();
    $this->actingAs($usuario);

    $respuesta = $this->get(route('estadisticas.excel'))->assertOk()->assertDownload('estadisticas-sirse-usac-'.now()->format('d-m-Y').'.xlsx');
    $hojas = LectorExcel::hojas($respuesta);
    $filaEps = collect($hojas['EPS'])->firstWhere(0, $eps->estudiante->nombre_completo);

    expect(array_keys($hojas))->toBe(['Resumen', 'Departamentos', 'EPS', 'Articulación', 'Funciones articuladas', 'Poblaciones vulnerables'])
        ->and(collect($hojas['Resumen'])->firstWhere(0, 'Aporte (Q)')[1])->toBe('Q1,000.00')
        ->and(count($hojas['EPS']))->toBe(2)
        ->and($filaEps)->toMatchArray([1 => 'Ingeniería Civil', 5 => 'Petén', 6 => 'Flores', 7 => 4, 9 => 1000, 14 => 3, 15 => 'Articulada'])
        ->and(collect($hojas['Poblaciones vulnerables'])->firstWhere(0, 'Mujeres'))->toBe(['Mujeres', 1, 1, 4, 30, 1000])
        ->and(collect($hojas['Articulación'])->firstWhere(0, '3 de 4 · Articulada')[1])->toBe(1);

    expect(Bitacora::where('tipo_cambio', TipoCambioBitacora::Exportacion)->sole())
        ->usuario_correo->toBe($usuario->email)
        ->detalle->toContain('Exportó a Excel las estadísticas del país');
});

it('exporta un departamento por municipio', function () {
    epsParaExportar();
    $this->actingAs(User::factory()->administrador()->create());

    $hojas = LectorExcel::hojas($this->get(route('estadisticas.departamento.excel', '17'))->assertOk());

    expect(array_keys($hojas))->toBe(['Resumen', 'Municipios', 'Bienes y servicios', 'Investigaciones', 'Instituciones aliadas', 'EPS'])
        ->and($hojas['Resumen'][0])->toBe(['Departamento', 'Petén'])
        ->and(collect($hojas['Bienes y servicios'])->firstWhere(0, 'Flores'))->toBe(['Flores', 'Filtros de agua', 'Bien', 4, 30, 1000])
        ->and(collect($hojas['Investigaciones'])->firstWhere(0, 'Flores')[5])->toBe('15-03-2026');
});

it('exporta el aporte por ODS con todos sus bienes y servicios', function () {
    epsParaExportar();
    $this->actingAs(User::factory()->administrador()->create());

    $hojas = LectorExcel::hojas($this->get(route('ods.excel'))->assertOk()->assertDownload('ods-sirse-usac-'.now()->format('d-m-Y').'.xlsx'));

    expect(array_keys($hojas))->toBe(['ODS', 'Bienes y servicios por ODS', 'Líneas por ODS', 'Áreas por ODS', 'Unidades por ODS'])
        ->and(collect($hojas['ODS'])->firstWhere(1, 'Agua limpia y saneamiento'))->toMatchArray([0 => 6, 2 => 1000, 4 => 4, 5 => 30, 6 => 1])
        ->and(collect($hojas['Bienes y servicios por ODS'])->firstWhere(2, 'Filtros de agua'))->toBe([6, 'Agua limpia y saneamiento', 'Filtros de agua', 'Bien', 4, 30, 1000]);
});

it('aplica los filtros a la exportación', function () {
    $unidad = UnidadAcademica::factory()->create();
    epsParaExportar(['unidad_academica_id' => $unidad->id]);
    epsParaExportar();
    $this->actingAs(User::factory()->administrador()->create());

    $hojas = LectorExcel::hojas($this->get(route('estadisticas.excel', ['unidad' => $unidad->id])));

    expect(count($hojas['EPS']))->toBe(2)
        ->and(collect($hojas['Resumen'])->firstWhere(0, 'Unidad académica')[1])->toBe($unidad->nombre);
});
