<?php

use App\Enums\TipoUnidadAcademica;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function administradorUnidades(): User
{
    $usuario = User::factory()->administrador()->create();
    test()->actingAs($usuario);

    return $usuario;
}

/**
 * @param  array<string, mixed>  $cambios
 * @return array<string, mixed>
 */
function datosUnidad(array $cambios = []): array
{
    return [
        'nombre' => 'Facultad de Humanidades',
        'siglas' => 'FAHUSAC',
        'tipo' => 'facultad',
        'nombre_contacto' => 'Ana Ruiz',
        'correo_contacto' => 'humanidades@usac.example',
        'telefono_contacto' => '24188000',
        'direccion' => 'Edificio S-4, Ciudad Universitaria zona 12',
        'latitud' => 14.5866,
        'longitud' => -90.5527,
        'activa' => true,
        ...$cambios,
    ];
}

it('envía a los invitados a iniciar sesión', function () {
    $this->get(route('admin.unidades.index'))->assertRedirect(route('login'));
});

it('niega el acceso a quien no es administrador', function (string $estado) {
    $unidad = UnidadAcademica::factory()->create();
    $this->actingAs(User::factory()->{$estado}()->create());

    $this->get(route('admin.unidades.index'))->assertForbidden();
    $this->post(route('admin.unidades.store'), datosUnidad())->assertForbidden();
    $this->put(route('admin.unidades.update', $unidad), datosUnidad())->assertForbidden();
    $this->delete(route('admin.unidades.destroy', $unidad))->assertForbidden();

    expect(UnidadAcademica::count())->toBe(1);
})->with(['invitado', 'estudiante']);

it('lista las unidades con su ubicación, sus EPS y su administrador', function () {
    administradorUnidades();
    $unidad = UnidadAcademica::factory()->create(['nombre' => 'CUNOC', 'tipo' => TipoUnidadAcademica::CentroUniversitario, 'latitud' => 14.8353, 'longitud' => -91.5106]);
    Expediente::factory()->count(2)->create(['unidad_academica_id' => $unidad->id]);
    UnidadAcademica::factory()->create(['nombre' => 'Facultad de Agronomía']);

    $this->get(route('admin.unidades.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/unidades')
            ->has('unidades', 2)
            ->has('tipos', 4)
            ->where('unidades.0.nombre', 'CUNOC')
            ->where('unidades.0.tipo_etiqueta', 'Centro universitario')
            ->where('unidades.0.latitud', '14.8353000')
            ->where('unidades.0.expedientes', 2)
            ->where('unidades.1.latitud', null));
});

it('agrega una unidad con las coordenadas de su edificio', function () {
    administradorUnidades();

    $this->post(route('admin.unidades.store'), datosUnidad())
        ->assertRedirect(route('admin.unidades.index'))
        ->assertSessionHasNoErrors();

    expect(UnidadAcademica::sole())
        ->nombre->toBe('Facultad de Humanidades')
        ->tipo->toBe(TipoUnidadAcademica::Facultad)
        ->latitud->toBe('14.5866000')
        ->longitud->toBe('-90.5527000')
        ->activa->toBeTrue();
});

it('agrega una unidad sin coordenadas', function () {
    administradorUnidades();

    $this->post(route('admin.unidades.store'), datosUnidad(['latitud' => null, 'longitud' => null]))->assertSessionHasNoErrors();

    expect(UnidadAcademica::sole()->latitud)->toBeNull();
});

it('valida la unidad', function (array $cambios, array $errores) {
    administradorUnidades();
    UnidadAcademica::factory()->create(['nombre' => 'Facultad de Agronomía']);

    $this->post(route('admin.unidades.store'), datosUnidad($cambios))->assertSessionHasErrors($errores);

    expect(UnidadAcademica::count())->toBe(1);
})->with([
    'sin nombre' => [['nombre' => ''], ['nombre' => 'Ingresa el nombre.']],
    'nombre repetido' => [['nombre' => 'Facultad de Agronomía'], ['nombre' => 'Ya existe una unidad académica con este nombre.']],
    'tipo inválido' => [['tipo' => 'colegio'], ['tipo']],
    'correo inválido' => [['correo_contacto' => 'no-es-correo'], ['correo_contacto']],
    'latitud sin longitud' => [['longitud' => null], ['longitud' => 'Ingresa la longitud junto con la latitud.']],
    'coordenadas fuera del país' => [['latitud' => 40.7, 'longitud' => -74.0], ['latitud', 'longitud']],
]);

it('actualiza una unidad conservando su propio nombre', function () {
    administradorUnidades();
    $unidad = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);

    $this->put(route('admin.unidades.update', $unidad), datosUnidad(['siglas' => 'HUM', 'activa' => false]))
        ->assertRedirect(route('admin.unidades.index'))
        ->assertSessionHasNoErrors();

    expect($unidad->fresh())->siglas->toBe('HUM')->activa->toBeFalse()->latitud->toBe('14.5866000');
});

it('elimina una unidad sin EPS ni administrador', function () {
    administradorUnidades();
    $unidad = UnidadAcademica::factory()->create();

    $this->delete(route('admin.unidades.destroy', $unidad))->assertRedirect(route('admin.unidades.index'));

    $this->assertSoftDeleted($unidad);
});

it('no elimina una unidad con EPS o con administrador: hay que desactivarla', function (string $motivo) {
    administradorUnidades();
    $unidad = UnidadAcademica::factory()->create();
    $motivo === 'eps'
        ? Expediente::factory()->create(['unidad_academica_id' => $unidad->id])
        : $unidad->update(['administrador_id' => User::factory()->create()->id]);

    $this->delete(route('admin.unidades.destroy', $unidad))
        ->assertRedirect(route('admin.unidades.index'))
        ->assertInertiaFlash('toast.type', 'error');

    expect($unidad->fresh()->trashed())->toBeFalse();
})->with(['eps', 'administrador']);
