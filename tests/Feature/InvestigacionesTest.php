<?php

use App\Enums\EstadoExpediente;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * Publicación de investigación de un EPS en el estado indicado.
 *
 * @param  array<string, mixed>  $publicacion
 * @param  array<string, mixed>  $expediente
 */
function investigacionDeEps(EstadoExpediente $estado, array $publicacion = [], array $expediente = []): Expediente
{
    $eps = Expediente::factory()->create(['estado_expediente' => $estado, ...$expediente]);
    $eps->publicaciones()->create(['titulo' => 'Lectura temprana', 'tipo' => 'articulo', 'autores' => 'A. Pérez', ...$publicacion]);

    return $eps;
}

it('envía a quienes no tienen sesión a iniciar sesión', function () {
    $this->get(route('investigaciones.index'))->assertRedirect(route('login'));
});

it('muestra a cualquier usuario con sesión las investigaciones de los EPS aprobados', function () {
    $aprobado = investigacionDeEps(EstadoExpediente::Verificado, [
        'titulo' => 'Agua segura en Petén',
        'tipo' => 'tesis',
        'resumen' => 'Estudio del acceso al agua.',
        'enlace' => 'https://revista.usac.edu.gt/agua',
        'fecha_publicacion' => '2026-05-10',
    ], ['nombre_carrera' => 'Ingeniería Civil']);
    investigacionDeEps(EstadoExpediente::Completo, ['titulo' => 'En revisión']);
    investigacionDeEps(EstadoExpediente::Activo, ['titulo' => 'En progreso']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('investigaciones.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('investigaciones/index')
            ->has('investigaciones', 1)
            ->where('investigaciones.0.titulo', 'Agua segura en Petén')
            ->where('investigaciones.0.tipo', 'Tesis')
            ->where('investigaciones.0.carrera', 'Ingeniería Civil')
            ->where('investigaciones.0.enlace', 'https://revista.usac.edu.gt/agua')
            ->where('investigaciones.0.fecha', '2026-05-10')
            ->where('investigaciones.0.expediente_id', $aprobado->id));
});

it('busca por texto y filtra por tipo y por unidad', function (Closure $filtros, string $esperado) {
    $humanidades = UnidadAcademica::factory()->create();
    investigacionDeEps(EstadoExpediente::Verificado, ['titulo' => 'Lectura en el aula', 'tipo' => 'articulo', 'autores' => 'Ana Pérez'], ['unidad_academica_id' => $humanidades->id]);
    investigacionDeEps(EstadoExpediente::Verificado, ['titulo' => 'Suelos del altiplano', 'tipo' => 'tesis', 'resumen' => 'Fertilidad y riego']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('investigaciones.index', $filtros($humanidades)))
        ->assertInertia(fn (Assert $page) => $page->has('investigaciones', 1)->where('investigaciones.0.titulo', $esperado));
})->with([
    'por autor' => [fn () => ['q' => 'Ana'], 'Lectura en el aula'],
    'por resumen' => [fn () => ['q' => 'riego'], 'Suelos del altiplano'],
    'por tipo' => [fn () => ['tipo' => 'tesis'], 'Suelos del altiplano'],
    'por unidad' => [fn (UnidadAcademica $unidad) => ['unidad' => $unidad->id], 'Lectura en el aula'],
]);

it('rechaza un tipo de publicación inexistente', function () {
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('investigaciones.index', ['tipo' => 'novela']))->assertSessionHasErrors('tipo');
});
