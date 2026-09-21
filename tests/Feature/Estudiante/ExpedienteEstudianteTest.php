<?php

use App\Enums\EstadoExpediente;
use App\Enums\TipoUnidadAcademica;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

it('lista las carreras del estudiante para elegir', function () {
    RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());

    $this->get(route('estudiante.carreras'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('estudiante/carreras')
            ->has('carreras', 2)
            ->where('carreras.0.clave', '07-00-28')
            ->where('carreras.0.graduado', true)
            ->where('carreras.0.expediente', null)
            ->where('carreras.1.clave', '77-00-66')
            ->where('carreras.1.nombre_carrera', 'Licenciatura en Pedagogía y Administración Educativa')
            ->where('carreras.1.ciclo_activo', '2017'));
});

it('no consulta el servicio mientras la información de las carreras es reciente', function () {
    RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());
    $this->get(route('estudiante.carreras'))->assertOk();

    expect(RegistroAcademicoFalso::solicitudes())->toBeEmpty();
});

it('actualiza las carreras desde el registro académico cuando la información es antigua', function () {
    $estudiante = Estudiante::factory()->create(['carnet' => RegistroAcademicoFalso::CARNET]);
    RegistroAcademicoFalso::iniciarSesion($estudiante);
    $estudiante->update(['carreras' => [RegistroAcademicoFalso::carreras()[0]], 'ultima_consulta_at' => now()->subDays(2)]);
    RegistroAcademicoFalso::responder();

    $this->get(route('estudiante.carreras'))
        ->assertInertia(fn (Assert $page) => $page->has('carreras', 2));

    expect($estudiante->fresh()->ultima_consulta_at->isToday())->toBeTrue();
});

it('conserva las carreras guardadas cuando el registro académico no responde', function () {
    $estudiante = Estudiante::factory()->create(['carnet' => RegistroAcademicoFalso::CARNET]);
    RegistroAcademicoFalso::iniciarSesion($estudiante);
    $estudiante->update(['ultima_consulta_at' => now()->subDays(2)]);
    RegistroAcademicoFalso::fallar();

    $this->get(route('estudiante.carreras'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('carreras', 2));
});

it('crea el expediente de la carrera elegida y su unidad académica', function () {
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    $this->post(route('estudiante.expedientes.store'), ['carrera' => '77-00-66'])
        ->assertRedirect(route('estudiante.expedientes.bienes-servicios.index', Expediente::first()));

    $expediente = Expediente::first();

    expect($expediente)
        ->estudiante_id->toBe($estudiante->id)
        ->codigo_unidad->toBe('77')
        ->codigo_extension->toBe('00')
        ->codigo_carrera->toBe('66')
        ->nombre_carrera->toBe('Licenciatura en Pedagogía y Administración Educativa')
        ->nombre_extension->toBe('Sede  Guatemala. -Plan diario-')
        ->nivel_academico->toBe('Licenciatura')
        ->eje_actual->toBe(1)
        ->estado_expediente->toBe(EstadoExpediente::Activo);

    expect($expediente->unidadAcademica)
        ->nombre->toBe('Facultad de Humanidades')
        ->tipo->toBe(TipoUnidadAcademica::Facultad)
        ->activa->toBeTrue();
});

it('reutiliza la unidad académica existente aunque la carrera tenga otro código de unidad', function () {
    $estudiante = Estudiante::factory()->create();
    $unidad = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    $this->post(route('estudiante.expedientes.store'), ['carrera' => '07-00-28']);
    $this->post(route('estudiante.expedientes.store'), ['carrera' => '77-00-66']);

    expect(UnidadAcademica::count())->toBe(1)
        ->and(Expediente::pluck('unidad_academica_id')->unique()->all())->toBe([$unidad->id]);
});

it('permite un expediente por cada carrera sin duplicar el de una carrera ya iniciada', function () {
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    $this->post(route('estudiante.expedientes.store'), ['carrera' => '07-00-28']);
    $this->post(route('estudiante.expedientes.store'), ['carrera' => '77-00-66']);
    $this->post(route('estudiante.expedientes.store'), ['carrera' => '77-00-66']);

    expect(Expediente::count())->toBe(2)
        ->and($estudiante->expedientes()->pluck('codigo_carrera')->sort()->values()->all())->toBe(['28', '66']);
});

it('infiere el tipo de la unidad académica a partir de su nombre', function (string $nombre, TipoUnidadAcademica $tipo) {
    expect(TipoUnidadAcademica::desdeNombre($nombre))->toBe($tipo);
})->with([
    ['Facultad de Humanidades', TipoUnidadAcademica::Facultad],
    ['Escuela de Ciencias Psicológicas', TipoUnidadAcademica::Escuela],
    ['Centro Universitario de Occidente', TipoUnidadAcademica::CentroUniversitario],
    ['Instituto de Investigaciones', TipoUnidadAcademica::Otro],
]);

it('rechaza una carrera que no pertenece al estudiante', function (string $carrera) {
    RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());

    $this->post(route('estudiante.expedientes.store'), ['carrera' => $carrera])
        ->assertSessionHasErrors(['carrera']);

    expect(Expediente::count())->toBe(0);
})->with([
    'clave inexistente' => '99-00-99',
    'vacía' => '',
]);

it('muestra el avance del expediente ya iniciado y permite continuarlo', function () {
    $estudiante = Estudiante::factory()->create();
    $expediente = Expediente::factory()->for($estudiante)->create([
        'codigo_unidad' => '77',
        'codigo_extension' => '00',
        'codigo_carrera' => '66',
        'eje_actual' => 4,
    ]);
    $expediente->bienesServicios()->createMany([
        ['tipo' => 'bien', 'descripcion' => 'Mobiliario', 'fecha' => '2026-03-01'],
        ['tipo' => 'servicio', 'descripcion' => 'Jornada médica', 'fecha' => '2026-03-02'],
    ]);
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    $this->get(route('estudiante.carreras'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('carreras.0.expediente', null)
            ->where('carreras.1.expediente', [
                'id' => $expediente->id,
                'eje_actual' => 4,
                'estado' => 'activo',
                'estado_etiqueta' => 'En progreso',
                'registros' => 2,
            ]));

    $this->get(route('estudiante.expedientes.show', $expediente))
        ->assertRedirect(route('estudiante.expedientes.territorio.index', $expediente));
});

it('lleva al resumen a quien retoma un expediente ya completo', function () {
    $estudiante = Estudiante::factory()->create();
    $expediente = Expediente::factory()->for($estudiante)->create(['estado_expediente' => EstadoExpediente::Completo]);
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    $this->get(route('estudiante.expedientes.show', $expediente))
        ->assertRedirect(route('estudiante.expedientes.cierre.index', $expediente));
});

it('oculta los expedientes de otros estudiantes', function () {
    $ajeno = Expediente::factory()->create();
    RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());

    $this->get(route('estudiante.expedientes.show', $ajeno))->assertNotFound();
});
