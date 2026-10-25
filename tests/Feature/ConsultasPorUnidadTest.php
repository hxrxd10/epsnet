<?php

use App\Enums\EstadoExpediente;
use App\Models\Carrera;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\LectorExcel;

/**
 * EPS aprobado con un bien o servicio, una publicación y su ubicación en Sacatepéquez.
 *
 * @param  array<string, mixed>  $atributos
 */
function epsDeUnidad(UnidadAcademica $unidad, string $titulo, array $atributos = []): Expediente
{
    $eps = Expediente::factory()->for(Estudiante::factory())->create([
        'unidad_academica_id' => $unidad->id,
        'estado_expediente' => EstadoExpediente::Verificado,
        'verificado_at' => '2026-05-01',
        ...$atributos,
    ]);
    $eps->ubicaciones()->create(['departamento_id' => Departamento::firstOrCreate(['codigo' => '03'], ['nombre' => 'Sacatepéquez', 'cabecera' => 'Antigua Guatemala'])->id]);
    $eps->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada', 'fecha' => '2026-03-01']);
    $eps->publicaciones()->create(['titulo' => $titulo, 'tipo' => 'articulo', 'autores' => 'A. Pérez']);

    return $eps;
}

/**
 * Humanidades con dos carreras (Pedagogía 07-00-28 y Psicología 07-00-30) y Agronomía; un EPS aprobado en cada una.
 *
 * @return array{humanidades: UnidadAcademica, pedagogia: Expediente, psicologia: Expediente, agronomia: Expediente}
 */
function escenarioDeUnidades(): array
{
    $humanidades = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
    $agronomia = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Agronomía']);

    return [
        'humanidades' => $humanidades,
        'pedagogia' => epsDeUnidad($humanidades, 'Lectura temprana', ['codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '28', 'nombre_carrera' => 'Pedagogía']),
        'psicologia' => epsDeUnidad($humanidades, 'Salud mental', ['codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '30', 'nombre_carrera' => 'Psicología']),
        'agronomia' => epsDeUnidad($agronomia, 'Suelos', ['nombre_carrera' => 'Agronomía']),
    ];
}

it('muestra a una unidad académica solo lo de su unidad en todas las consultas', function () {
    ['humanidades' => $humanidades, 'agronomia' => $agronomia] = escenarioDeUnidades();
    $this->actingAs(User::factory()->unidadAcademica($humanidades)->create());

    $this->get(route('estadisticas.index'))->assertInertia(fn (Assert $page) => $page
        ->where('totales.eps', 2)
        ->where('unidades', [['value' => (string) $humanidades->id, 'label' => 'Facultad de Humanidades']])
        ->where('carreras', fn ($carreras) => collect($carreras)->pluck('value')->all() === ['Pedagogía', 'Psicología'])
        ->where('alcanceConsulta', 'Facultad de Humanidades'));
    $this->get(route('estadisticas.departamento', '03'))->assertInertia(fn (Assert $page) => $page->where('metricas.eps', 2));
    $this->get(route('ods.index'))->assertInertia(fn (Assert $page) => $page->where('totales.eps', 2));
    $this->get(route('repositorio.index'))->assertInertia(fn (Assert $page) => $page->has('expedientes', 2)->has('unidades', 1));
    $this->get(route('investigaciones.index'))->assertInertia(fn (Assert $page) => $page
        ->where('investigaciones', fn ($lista) => collect($lista)->pluck('titulo')->sort()->values()->all() === ['Lectura temprana', 'Salud mental']));
    $this->get(route('repositorio.show', $agronomia))->assertNotFound();
});

it('limita a un supervisor a las carreras que tiene asignadas', function () {
    ['humanidades' => $humanidades, 'pedagogia' => $pedagogia, 'psicologia' => $psicologia] = escenarioDeUnidades();
    $supervisor = User::factory()->unidadAcademica($humanidades)->create();
    $supervisor->carreras()->attach(Carrera::factory()->create(['unidad_academica_id' => $humanidades->id, 'codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '28', 'nombre' => 'Pedagogía']));
    $this->actingAs($supervisor);

    $this->get(route('estadisticas.index'))->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1));
    $this->get(route('ods.index'))->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1));
    $this->get(route('repositorio.index'))->assertInertia(fn (Assert $page) => $page->has('expedientes', 1)->where('expedientes.0.id', $pedagogia->id));
    $this->get(route('investigaciones.index'))->assertInertia(fn (Assert $page) => $page->has('investigaciones', 1)->where('investigaciones.0.titulo', 'Lectura temprana'));
    $this->get(route('repositorio.show', $psicologia))->assertNotFound();
    $this->get(route('repositorio.show', $pedagogia))->assertOk();
});

it('exporta a Excel solo lo de su alcance e indica cuál es', function () {
    ['humanidades' => $humanidades] = escenarioDeUnidades();
    $this->actingAs(User::factory()->unidadAcademica($humanidades)->create());

    $hojas = LectorExcel::hojas($this->get(route('estadisticas.excel'))->assertOk());

    expect(count($hojas['EPS']))->toBe(3)
        ->and(collect($hojas['Resumen'])->firstWhere(0, 'Alcance')[1])->toBe('Facultad de Humanidades');
});

it('muestra todo el país a DIGEU y a los estudiantes', function (string $rol) {
    escenarioDeUnidades();
    $this->actingAs(User::factory()->{$rol}()->create());

    $this->get(route('estadisticas.index'))->assertInertia(fn (Assert $page) => $page->where('totales.eps', 3)->where('alcanceConsulta', null));
    $this->get(route('repositorio.index'))->assertInertia(fn (Assert $page) => $page->has('expedientes', 3));
})->with(['administrador', 'estudiante']);

it('no muestra nada a una unidad académica sin unidad asignada', function () {
    escenarioDeUnidades();
    $this->actingAs(User::factory()->unidadAcademica()->create());

    $this->get(route('estadisticas.index'))->assertInertia(fn (Assert $page) => $page->where('totales.eps', 0));
    $this->get(route('repositorio.index'))->assertInertia(fn (Assert $page) => $page->has('expedientes', 0));
});
