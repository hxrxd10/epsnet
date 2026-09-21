<?php

use App\Enums\ProgramaEps;
use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\Estudiante;
use App\Models\Expediente;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

function expedienteConSesion(): Expediente
{
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    return Expediente::factory()->for($estudiante)->create();
}

it('marca el EPS como parte del EPSUM y lo devuelve a EPS normal', function () {
    $expediente = expedienteConSesion();

    $this->put(route('estudiante.expedientes.programa.update', $expediente), ['epsum' => true])->assertRedirect()->assertSessionHasNoErrors();
    expect($expediente->fresh()->programa)->toBe(ProgramaEps::Epsum);

    $this->put(route('estudiante.expedientes.programa.update', $expediente), ['epsum' => false])->assertRedirect();
    expect($expediente->fresh()->programa)->toBe(ProgramaEps::EpsFacultativo);
});

it('trata como EPS normal el que aún no eligió programa y lo informa en el primer paso', function () {
    $expediente = expedienteConSesion();

    $this->get(route('estudiante.expedientes.bienes-servicios.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page->where('expediente.es_epsum', false));

    $this->put(route('estudiante.expedientes.programa.update', $expediente), ['epsum' => true]);

    $this->get(route('estudiante.expedientes.bienes-servicios.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page->where('expediente.es_epsum', true));
});

it('exige un valor válido', function (array $cuerpo) {
    $expediente = expedienteConSesion();

    $this->put(route('estudiante.expedientes.programa.update', $expediente), $cuerpo)->assertSessionHasErrors('epsum');

    expect($expediente->fresh()->programa)->toBeNull();
})->with([
    'sin valor' => [[]],
    'texto' => [['epsum' => 'quizás']],
]);

it('no permite cambiar el programa del EPS de otro estudiante', function () {
    expedienteConSesion();
    $ajeno = Expediente::factory()->create();

    $this->put(route('estudiante.expedientes.programa.update', $ajeno), ['epsum' => true])->assertNotFound();

    expect($ajeno->fresh()->programa)->toBeNull();
});

it('deja constancia del cambio de programa en la bitácora', function () {
    $expediente = expedienteConSesion();

    $this->put(route('estudiante.expedientes.programa.update', $expediente), ['epsum' => true]);

    $registro = Bitacora::where('modulo', 'Expedientes (EPS)')->where('tipo_cambio', TipoCambioBitacora::Edicion)->sole();

    expect($registro->valores_nuevos)->toBe(['programa' => 'epsum'])
        ->and($registro->valores_anteriores)->toBe(['programa' => null]);
});
