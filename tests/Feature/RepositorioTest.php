<?php

use App\Enums\EstadoExpediente;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use App\Models\Municipio;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function expedienteAprobado(array $atributos = []): Expediente
{
    return Expediente::factory()
        ->for(Estudiante::factory()->create(['nombre1' => 'Madeley', 'apellido1' => 'Morales']))
        ->create([
            'estado_expediente' => EstadoExpediente::Verificado,
            'verificado_at' => now(),
            ...$atributos,
        ]);
}

it('envía a los invitados a iniciar sesión', function () {
    $this->get(route('repositorio.index'))->assertRedirect(route('login'));
    $this->get(route('repositorio.show', expedienteAprobado()))->assertRedirect(route('login'));
});

it('lista solo los EPS aprobados, para cualquier usuario con sesión', function (string $estado) {
    expedienteAprobado();
    expedienteAprobado();
    Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Completo]);
    Expediente::factory()->create();
    $this->actingAs(User::factory()->{$estado}()->create());

    $this->get(route('repositorio.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('repositorio/index')
            ->has('expedientes', 2)
            ->where('pagina.total', 2));
})->with(['invitado', 'estudiante', 'administrador']);

it('describe lo realizado con la evaluación de impacto o, si no hay, con lo primero registrado', function () {
    $conImpacto = expedienteAprobado();
    $conImpacto->bienesServicios()->create(['tipo' => 'bien', 'descripcion' => 'Mobiliario escolar', 'fecha' => '2026-03-01']);
    $conImpacto->seguimientos()->create(['tipo_registro' => 'evaluacion_final', 'indicador' => 'Impacto', 'evaluacion_impacto' => 'Mejoró la lectura de 40 niños', 'fecha' => '2026-08-01']);
    $sinImpacto = expedienteAprobado();
    $sinImpacto->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada médica', 'fecha' => '2026-03-01']);
    $vacio = expedienteAprobado();
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('repositorio.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('expedientes', fn ($lista) => collect($lista)->pluck('descripcion', 'id')->all() === [
                $vacio->id => null,
                $sinImpacto->id => 'Jornada médica',
                $conImpacto->id => 'Mejoró la lectura de 40 niños',
            ]));
});

it('muestra el estudiante, la carrera, la unidad y la ubicación', function () {
    $expediente = expedienteAprobado(['nombre_carrera' => 'Licenciatura en Pedagogía']);
    $antigua = Municipio::factory()->for(Departamento::factory()->create(['nombre' => 'Sacatepéquez']))->create(['nombre' => 'Antigua Guatemala']);
    $expediente->ubicaciones()->create(['departamento_id' => $antigua->departamento_id, 'municipio_id' => $antigua->id]);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('repositorio.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('expedientes.0.carrera', 'Licenciatura en Pedagogía')
            ->where('expedientes.0.estudiante', "Madeley Morales {$expediente->estudiante->apellido2}")
            ->where('expedientes.0.ubicacion', 'Antigua Guatemala, Sacatepéquez')
            ->where('expedientes.0.unidad', $expediente->unidadAcademica->nombre));
});

it('filtra por texto y por unidad académica', function () {
    $humanidades = UnidadAcademica::factory()->create();
    $ingenieria = UnidadAcademica::factory()->create();
    expedienteAprobado(['unidad_academica_id' => $humanidades->id, 'nombre_carrera' => 'Pedagogía']);
    expedienteAprobado(['unidad_academica_id' => $ingenieria->id, 'nombre_carrera' => 'Ingeniería Civil']);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('repositorio.index', ['q' => 'Civil']))->assertInertia(fn (Assert $page) => $page->has('expedientes', 1)->where('expedientes.0.carrera', 'Ingeniería Civil'));
    $this->get(route('repositorio.index', ['unidad' => $humanidades->id]))->assertInertia(fn (Assert $page) => $page->has('expedientes', 1)->where('expedientes.0.carrera', 'Pedagogía'));
});

it('muestra el detalle de un EPS aprobado', function () {
    $expediente = expedienteAprobado();
    $expediente->transferencias()->create(['tipo_actividad' => 'taller', 'actividad' => 'Taller para docentes', 'comunidad' => 'Aldea El Progreso', 'fecha' => '2026-03-05']);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('repositorio.show', $expediente))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('repositorio/show')
            ->where('expediente.id', $expediente->id)
            ->has('ejes', 6)
            ->where('ejes.2.registros.0.textos.0.valor', 'Taller para docentes'));
});

it('no muestra el detalle de un EPS que no está aprobado', function (EstadoExpediente $estado) {
    $expediente = Expediente::factory()->create(['estado_expediente' => $estado]);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('repositorio.show', $expediente))->assertNotFound();
})->with([EstadoExpediente::Activo, EstadoExpediente::Completo]);

it('no expone datos privados del estudiante ni de las instituciones', function () {
    $expediente = expedienteAprobado();
    $institucion = InstitucionAliada::factory()->create(['correo_contacto' => 'privado@escuela.example', 'telefono_contacto' => '22334455']);
    $expediente->alianzas()->create(['institucion_aliada_id' => $institucion->id]);
    $expediente->actores()->create(['institucion_receptora' => 'Escuela Norte', 'contraparte' => 'Directora', 'comunidad_beneficiada' => 'Barrio Norte']);
    $this->actingAs(User::factory()->invitado()->create());

    $contenido = $this->get(route('repositorio.show', $expediente))->getContent().$this->get(route('repositorio.index'))->getContent();

    expect($contenido)->not->toContain('privado@escuela.example')
        ->not->toContain('22334455')
        ->not->toContain($expediente->estudiante->carnet);
});

it('muestra la documentación a cualquier usuario con sesión y redirige a quien no ha iniciado sesión', function () {
    $this->get(route('documentacion'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->invitado()->create())
        ->get(route('documentacion'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('documentacion')->where('rol', 'invitado'));
});
