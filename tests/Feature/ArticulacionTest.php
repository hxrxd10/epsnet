<?php

use App\Enums\EstadoExpediente;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

/**
 * EPS aprobado con un bien o servicio y una institución aliada: articula 2 de 4 funciones.
 */
function epsArticulado(): Expediente
{
    $eps = Expediente::factory()->for(Estudiante::factory())->create(['estado_expediente' => EstadoExpediente::Verificado, 'verificado_at' => now()]);
    $eps->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada', 'fecha' => '2026-03-01']);
    $eps->alianzas()->create(['institucion_aliada_id' => InstitucionAliada::factory()->create()->id]);

    return $eps;
}

it('calcula cuántas de las cuatro funciones articula cada EPS', function (Closure $preparar, int $nivel, string $etiqueta) {
    $eps = Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Verificado]);
    $preparar($eps);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('repositorio.show', $eps))
        ->assertInertia(fn (Assert $page) => $page->where('articulacion.nivel', $nivel)->where('articulacion.total', 4)->where('articulacion.etiqueta', $etiqueta));
})->with([
    'nada registrado' => [fn () => null, 0, 'Sin articulación'],
    'solo extensión' => [fn (Expediente $eps) => $eps->bienesServicios()->create(['tipo' => 'bien', 'descripcion' => 'X', 'fecha' => '2026-03-01']), 1, 'Inicial'],
    'investigación y transferencia' => [function (Expediente $eps): void {
        $eps->publicaciones()->create(['titulo' => 'T', 'tipo' => 'articulo', 'autores' => 'A']);
        $eps->transferencias()->create(['tipo_actividad' => 'taller', 'actividad' => 'Taller', 'comunidad' => 'X', 'fecha' => '2026-03-01']);
    }, 2, 'Básica'],
    'las cuatro funciones' => [function (Expediente $eps): void {
        $eps->bienesServicios()->create(['tipo' => 'bien', 'descripcion' => 'X', 'fecha' => '2026-03-01']);
        $eps->publicaciones()->create(['titulo' => 'T', 'tipo' => 'articulo', 'autores' => 'A']);
        $eps->transferencias()->create(['tipo_actividad' => 'taller', 'actividad' => 'Taller', 'comunidad' => 'X', 'fecha' => '2026-03-01']);
        $eps->alianzas()->create(['institucion_aliada_id' => InstitucionAliada::factory()->create()->id]);
    }, 4, 'Integral'],
]);

it('indica qué funciones articula el EPS y cuáles le faltan', function () {
    $eps = epsArticulado();
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('panel.estudiantes.show', $eps))
        ->assertInertia(fn (Assert $page) => $page
            ->where('articulacion.nivel', 2)
            ->where('articulacion.porcentaje', 50)
            ->where('articulacion.funciones', fn ($funciones) => collect($funciones)->pluck('presente', 'clave')->all() === [
                'extension' => true, 'investigacion' => false, 'transferencia' => false, 'vinculacion' => true,
            ]));
});

it('muestra la articulación en los listados del panel y del repositorio y en el cierre del estudiante', function () {
    $eps = epsArticulado();

    $this->actingAs(User::factory()->administrador()->create());
    $this->get(route('panel.estudiantes.index'))->assertInertia(fn (Assert $page) => $page->where('expedientes.0.articulacion.nivel', 2));
    $this->get(route('repositorio.index'))->assertInertia(fn (Assert $page) => $page->where('expedientes.0.articulacion.nivel', 2));

    RegistroAcademicoFalso::iniciarSesion($eps->estudiante);
    $this->get(route('estudiante.expedientes.cierre.index', $eps))->assertInertia(fn (Assert $page) => $page->where('articulacion.nivel', 2));
});
