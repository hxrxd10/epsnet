<?php

use App\Enums\EstadoExpediente;
use App\Enums\TipoCambioBitacora;
use App\Models\Adjunto;
use App\Models\Bitacora;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

/**
 * Expediente del estudiante con sesión, con un registro en algún eje.
 *
 * @param  array<string, mixed>  $atributos
 */
function expedienteSolicitante(array $atributos = [], bool $conRegistros = true): Expediente
{
    $estudiante = Estudiante::factory()->create(['nombre1' => 'Madeley', 'apellido1' => 'Morales', 'apellido2' => 'Vivar']);
    RegistroAcademicoFalso::iniciarSesion($estudiante);
    $expediente = Expediente::factory()->for($estudiante)->create(['nombre_carrera' => 'Licenciatura en Pedagogía', ...$atributos]);
    $conRegistros && $expediente->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada', 'fecha' => '2026-03-01']);

    return $expediente;
}

it('envía el EPS a la bandeja de supervisión, con o sin orden de impresión', function (bool $conOrden) {
    $expediente = expedienteSolicitante();
    $conOrden && Adjunto::factory()->create(['entidad_id' => $expediente->id]);

    $this->post(route('estudiante.expedientes.solicitud.store', $expediente))
        ->assertRedirect(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertSessionHasNoErrors();

    expect($expediente->fresh())
        ->aprobacion_solicitada_at->not->toBeNull()
        ->completado_at->not->toBeNull()
        ->estado_expediente->toBe(EstadoExpediente::Completo)
        ->and(Expediente::pendientesDeAprobacion()->pluck('id')->all())->toBe([$expediente->id])
        ->and(Expediente::validos()->count())->toBe(0);
})->with(['con orden de impresión' => true, 'sin orden de impresión' => false]);

it('deja constancia del envío en la bitácora', function () {
    $expediente = expedienteSolicitante();
    $usuario = auth()->user();

    $this->post(route('estudiante.expedientes.solicitud.store', $expediente));

    $registro = Bitacora::where('tipo_cambio', TipoCambioBitacora::SolicitudAprobacion)->sole();

    expect($registro)->usuario_correo->toBe($usuario->email)->usuario_rol->toBe('estudiante')
        ->and($registro->detalle)->toContain('Envió a aprobación de su unidad académica el EPS de Madeley Morales Vivar (Licenciatura en Pedagogía)')
        ->toContain('(sin orden de impresión)');
});

it('ofrece el envío en el cierre y, una vez enviado, muestra su lugar en la bandeja de la unidad', function () {
    $unidad = UnidadAcademica::factory()->create();
    Expediente::factory()->create(['unidad_academica_id' => $unidad->id, 'aprobacion_solicitada_at' => now()->subDay()]);
    $expediente = expedienteSolicitante(['unidad_academica_id' => $unidad->id]);
    Expediente::factory()->create(['unidad_academica_id' => $unidad->id, 'aprobacion_solicitada_at' => now()->addHour()]);
    Expediente::factory()->create(['aprobacion_solicitada_at' => now()->subDays(5)]);

    $this->get(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page->where('puede_solicitar', true)->where('solicitud', null));

    $this->post(route('estudiante.expedientes.solicitud.store', $expediente));

    $this->get(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page
            ->where('puede_solicitar', false)
            ->where('solicitud.posicion', 2)
            ->where('solicitud.total', 3));
});

it('no ofrece el envío a quien ya envió o aprobó su EPS', function (array $atributos) {
    $expediente = expedienteSolicitante($atributos);

    $this->get(route('estudiante.expedientes.cierre.index', $expediente))->assertInertia(fn (Assert $page) => $page->where('puede_solicitar', false));
})->with([
    'EPS en revisión' => [['estado_expediente' => EstadoExpediente::Completo]],
    'EPS aprobado' => [['estado_expediente' => EstadoExpediente::Verificado]],
]);

it('rechaza el envío cuando no corresponde', function (array $atributos, bool $conRegistros, string $campo) {
    $expediente = expedienteSolicitante($atributos, $conRegistros);

    $this->post(route('estudiante.expedientes.solicitud.store', $expediente))->assertSessionHasErrors($campo);

    expect($expediente->fresh()->aprobacion_solicitada_at)->toBeNull()
        ->and(Bitacora::where('tipo_cambio', TipoCambioBitacora::SolicitudAprobacion)->count())->toBe(0);
})->with([
    'ya está en revisión' => [['estado_expediente' => EstadoExpediente::Completo], true, 'solicitud'],
    'ya está aprobado' => [['estado_expediente' => EstadoExpediente::Verificado], true, 'solicitud'],
    'no registró nada' => [[], false, 'registros'],
]);

it('no permite enviar dos veces ni cambia la fecha de llegada', function () {
    $expediente = expedienteSolicitante(['estado_expediente' => EstadoExpediente::Completo, 'aprobacion_solicitada_at' => '2026-09-01 10:00:00']);

    $this->post(route('estudiante.expedientes.solicitud.store', $expediente))->assertSessionHasErrors('solicitud');

    expect($expediente->fresh()->aprobacion_solicitada_at->toDateTimeString())->toBe('2026-09-01 10:00:00');
});

it('no permite enviar el EPS de otro estudiante', function () {
    expedienteSolicitante();
    $ajeno = Expediente::factory()->create();
    $ajeno->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'X', 'fecha' => '2026-03-01']);

    $this->post(route('estudiante.expedientes.solicitud.store', $ajeno))->assertNotFound();

    expect($ajeno->fresh()->aprobacion_solicitada_at)->toBeNull();
});
