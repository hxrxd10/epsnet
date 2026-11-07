<?php

use App\Enums\EstadoExpediente;
use App\Models\BienServicio;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\User;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

function epsDelEstudiante(EstadoExpediente $estado): Expediente
{
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    return Expediente::factory()->for($estudiante)->create(['estado_expediente' => $estado]);
}

it('no deja cambiar nada de un EPS aprobado', function (string $metodo, string $ruta, bool $conRegistro) {
    $expediente = epsDelEstudiante(EstadoExpediente::Verificado);
    $bien = $expediente->bienesServicios()->create(['tipo' => 'bien', 'descripcion' => 'Pupitres', 'fecha' => '2026-03-01']);
    $parametros = $conRegistro ? ['expediente' => $expediente, 'registro' => $bien->id] : ['expediente' => $expediente];

    $this->{$metodo}(route($ruta, $parametros), ['descripcion' => 'Cambio', 'tipo' => 'bien', 'fecha' => '2026-03-02'])->assertForbidden();

    expect($bien->fresh()->descripcion)->toBe('Pupitres')
        ->and(BienServicio::count())->toBe(1)
        ->and($expediente->fresh()->estado_expediente)->toBe(EstadoExpediente::Verificado);
})->with([
    'crear un bien' => ['post', 'estudiante.expedientes.bienes-servicios.store', false],
    'editar un bien' => ['put', 'estudiante.expedientes.bienes-servicios.update', true],
    'eliminar un bien' => ['delete', 'estudiante.expedientes.bienes-servicios.destroy', true],
    'crear una publicación' => ['post', 'estudiante.expedientes.publicaciones.store', false],
    'crear una transferencia' => ['post', 'estudiante.expedientes.transferencias.store', false],
    'crear una ubicación' => ['post', 'estudiante.expedientes.territorio.store', false],
    'crear un actor' => ['post', 'estudiante.expedientes.actores.store', false],
    'crear una alianza' => ['post', 'estudiante.expedientes.alianzas.store', false],
    'crear un seguimiento' => ['post', 'estudiante.expedientes.seguimiento.store', false],
    'cambiar el programa' => ['put', 'estudiante.expedientes.programa.update', false],
    'subir la orden' => ['post', 'estudiante.expedientes.orden-impresion.store', false],
    'eliminar la orden' => ['delete', 'estudiante.expedientes.orden-impresion.destroy', false],
    'subir la foto' => ['post', 'estudiante.expedientes.foto.store', false],
    'eliminar la foto' => ['delete', 'estudiante.expedientes.foto.destroy', false],
]);

it('deja ver lo consignado en un EPS aprobado', function (string $ruta) {
    $expediente = epsDelEstudiante(EstadoExpediente::Verificado);

    $this->get(route($ruta, $expediente))->assertOk();
})->with([
    'estudiante.expedientes.bienes-servicios.index',
    'estudiante.expedientes.publicaciones.index',
    'estudiante.expedientes.transferencias.index',
    'estudiante.expedientes.territorio.index',
    'estudiante.expedientes.actores.index',
    'estudiante.expedientes.seguimiento.index',
    'estudiante.expedientes.cierre.index',
]);

it('deja corregir el EPS mientras no está aprobado', function (EstadoExpediente $estado) {
    $expediente = epsDelEstudiante($estado);

    $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), ['tipo' => 'bien', 'descripcion' => 'Pupitres', 'fecha' => '2026-03-01'])
        ->assertRedirect(route('estudiante.expedientes.bienes-servicios.index', $expediente));

    expect($expediente->bienesServicios()->count())->toBe(1);
})->with([EstadoExpediente::Activo, EstadoExpediente::Completo]);

it('vuelve a dejar corregir cuando la supervisión retira la aprobación', function () {
    $expediente = epsDelEstudiante(EstadoExpediente::Verificado);
    $estudiante = auth()->user();

    $this->actingAs(User::factory()->administrador()->create())
        ->delete(route('panel.estudiantes.verificacion.destroy', $expediente));

    $this->actingAs($estudiante)
        ->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), ['tipo' => 'bien', 'descripcion' => 'Pupitres', 'fecha' => '2026-03-01']);

    expect($expediente->fresh()->estado_expediente)->toBe(EstadoExpediente::Completo)
        ->and($expediente->bienesServicios()->count())->toBe(1);
});

it('no le revela a otro estudiante un EPS ajeno', function () {
    $expediente = epsDelEstudiante(EstadoExpediente::Activo);
    RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());

    $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [])->assertNotFound();
});
