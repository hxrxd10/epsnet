<?php

use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function administradorAliadas(): User
{
    $usuario = User::factory()->administrador()->create();
    test()->actingAs($usuario);

    return $usuario;
}

/**
 * @param  array<string, mixed>  $cambios
 * @return array<string, mixed>
 */
function datosAliada(array $cambios = []): array
{
    return [
        'nombre' => 'Ministerio de Educación',
        'tipo' => 'Educación',
        'nombre_contacto' => 'María López',
        'correo_contacto' => 'director@escuela.example',
        'telefono_contacto' => '55501234',
        'direccion' => 'Aldea El Progreso',
        ...$cambios,
    ];
}

it('envía a los invitados a iniciar sesión', function () {
    $this->get(route('admin.instituciones.index'))->assertRedirect(route('login'));
});

it('niega el acceso a quien no es administrador', function (string $estado) {
    $institucion = InstitucionAliada::factory()->create();
    $this->actingAs(User::factory()->{$estado}()->create());

    $this->get(route('admin.instituciones.index'))->assertForbidden();
    $this->post(route('admin.instituciones.store'), datosAliada())->assertForbidden();
    $this->put(route('admin.instituciones.update', $institucion), datosAliada())->assertForbidden();
    $this->delete(route('admin.instituciones.destroy', $institucion))->assertForbidden();

    expect(InstitucionAliada::count())->toBe(1);
})->with(['invitado', 'estudiante']);

it('lista las instituciones aliadas con su tipo y en cuántos EPS figuran', function () {
    administradorAliadas();
    $usada = InstitucionAliada::factory()->create(['nombre' => 'Centro de Salud', 'tipo' => 'Salud']);
    InstitucionAliada::factory()->create(['nombre' => 'Escuela', 'tipo' => 'Educación']);
    Expediente::factory()->count(2)->create()->each(fn (Expediente $expediente) => $expediente->alianzas()->create(['institucion_aliada_id' => $usada->id]));

    $this->get(route('admin.instituciones.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/instituciones')
            ->where('pagina.total', 2)
            ->where('instituciones.0.nombre', 'Centro de Salud')
            ->where('instituciones.0.eps', 2)
            ->where('instituciones.1.eps', 0)
            ->where('tipos', ['Educación', 'Salud']));
});

it('filtra por texto y por tipo, y pagina de 20 en 20', function () {
    administradorAliadas();
    InstitucionAliada::factory()->create(['nombre' => 'Municipalidad de Cobán', 'tipo' => 'Gobierno local', 'nombre_contacto' => 'Ana Ruiz']);
    InstitucionAliada::factory()->create(['nombre' => 'Escuela Norte', 'tipo' => 'Educación', 'correo_contacto' => 'norte@escuela.example']);

    $this->get(route('admin.instituciones.index', ['q' => 'Cobán']))->assertInertia(fn (Assert $page) => $page->has('instituciones', 1));
    $this->get(route('admin.instituciones.index', ['q' => 'ana ruiz']))->assertInertia(fn (Assert $page) => $page->has('instituciones', 1));
    $this->get(route('admin.instituciones.index', ['q' => 'norte@']))->assertInertia(fn (Assert $page) => $page->has('instituciones', 1));
    $this->get(route('admin.instituciones.index', ['tipo' => 'Educación']))->assertInertia(fn (Assert $page) => $page->has('instituciones', 1)->where('instituciones.0.nombre', 'Escuela Norte'));

    InstitucionAliada::factory()->count(25)->create();

    $this->get(route('admin.instituciones.index'))->assertInertia(fn (Assert $page) => $page->has('instituciones', 20)->where('pagina.ultima', 2)->where('pagina.total', 27));
});

it('agrega una institución y lo deja en la bitácora', function () {
    $administrador = administradorAliadas();

    $this->post(route('admin.instituciones.store'), datosAliada())->assertSessionHasNoErrors();

    expect(InstitucionAliada::sole())->nombre->toBe('Ministerio de Educación')->tipo->toBe('Educación')
        ->and(Bitacora::where('modulo', 'Instituciones aliadas')->sole())
        ->usuario_correo->toBe($administrador->email)
        ->tipo_cambio->toBe(TipoCambioBitacora::Creacion);
});

it('permite dejar sin datos de contacto a una institución', function () {
    administradorAliadas();

    $this->post(route('admin.instituciones.store'), ['nombre' => 'Escuela Sin Datos'])->assertSessionHasNoErrors();

    expect(InstitucionAliada::sole()->correo_contacto)->toBeNull();
});

it('valida la institución', function (array $cambios, array $errores) {
    administradorAliadas();
    InstitucionAliada::factory()->create(['nombre' => 'Escuela Repetida']);

    $this->post(route('admin.instituciones.store'), datosAliada($cambios))->assertSessionHasErrors($errores);

    expect(InstitucionAliada::count())->toBe(1);
})->with([
    'sin nombre' => [['nombre' => ''], ['nombre' => 'Ingresa el nombre.']],
    'nombre repetido' => [['nombre' => 'Escuela Repetida'], ['nombre' => 'Ya existe una institución aliada con este nombre.']],
    'correo inválido' => [['correo_contacto' => 'no-es-correo'], ['correo_contacto']],
]);

it('actualiza una institución conservando su propio nombre', function () {
    administradorAliadas();
    $institucion = InstitucionAliada::factory()->create(['nombre' => 'Escuela Vieja']);

    $this->put(route('admin.instituciones.update', $institucion), datosAliada(['nombre' => 'Escuela Vieja', 'tipo' => 'Salud']))
        ->assertSessionHasNoErrors();

    expect($institucion->fresh())->tipo->toBe('Salud');
});

it('elimina del todo una institución sin uso, liberando su nombre', function () {
    administradorAliadas();
    $institucion = InstitucionAliada::factory()->create(['nombre' => 'Escuela Temporal']);

    $this->delete(route('admin.instituciones.destroy', $institucion))->assertRedirect();

    expect(InstitucionAliada::withTrashed()->count())->toBe(0);

    $this->post(route('admin.instituciones.store'), datosAliada(['nombre' => 'Escuela Temporal']))->assertSessionHasNoErrors();
});

it('no elimina una institución aliada que figura en un EPS, ni siquiera eliminada', function (bool $eliminado) {
    administradorAliadas();
    $institucion = InstitucionAliada::factory()->create();
    $alianza = Expediente::factory()->create()->alianzas()->create(['institucion_aliada_id' => $institucion->id]);
    $eliminado && $alianza->delete();

    $this->delete(route('admin.instituciones.destroy', $institucion))->assertInertiaFlash('toast.type', 'error');

    expect(InstitucionAliada::count())->toBe(1);
})->with([false, true]);
