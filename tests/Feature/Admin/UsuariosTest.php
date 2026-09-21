<?php

use App\Enums\ClaveRol;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function iniciarComoAdministrador(): User
{
    $administrador = User::factory()->administrador()->create(['name' => 'Zoe Administradora']);
    test()->actingAs($administrador);

    return $administrador;
}

describe('acceso', function () {
    it('envía a los invitados a iniciar sesión', function () {
        $this->get(route('admin.usuarios.index'))->assertRedirect(route('login'));
    });

    it('niega el acceso a quien no es administrador', function (string $estado) {
        $this->actingAs(User::factory()->{$estado}()->create());
        $objetivo = User::factory()->invitado()->create();

        $this->get(route('admin.usuarios.index'))->assertForbidden();
        $this->put(route('admin.usuarios.update', $objetivo), ['rol' => 'digeu'])->assertForbidden();

        expect($objetivo->fresh()->tieneRol(ClaveRol::Invitado))->toBeTrue();
    })->with(['invitado', 'estudiante']);

    it('niega el acceso a una unidad académica', function () {
        $usuario = User::factory()->create();
        $usuario->forceFill(['rol_id' => Rol::delSistema(ClaveRol::UnidadAcademica)->id])->save();

        $this->actingAs($usuario)->get(route('admin.usuarios.index'))->assertForbidden();
    });
});

describe('listado', function () {
    it('lista a los usuarios con su rol y su unidad', function () {
        iniciarComoAdministrador();
        $unidad = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
        $coordinador = User::factory()->create(['name' => 'Beatriz Coordinadora']);
        $coordinador->forceFill(['rol_id' => Rol::delSistema(ClaveRol::UnidadAcademica)->id])->save();
        $unidad->forceFill(['administrador_id' => $coordinador->id])->save();
        User::factory()->invitado()->create(['name' => 'Ana Invitada']);

        $this->get(route('admin.usuarios.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/usuarios')
                ->has('usuarios', 3)
                ->where('usuarios.0.name', 'Ana Invitada')
                ->where('usuarios.0.rol', 'invitado')
                ->where('usuarios.1.name', 'Beatriz Coordinadora')
                ->where('usuarios.1.unidad', 'Facultad de Humanidades')
                ->where('rolesAsignables', fn ($roles) => collect($roles)->pluck('value')->sort()->values()->all() === ['digeu', 'invitado', 'unidad_academica']));
    });

    it('filtra por texto y por rol', function () {
        iniciarComoAdministrador();
        User::factory()->invitado()->create(['name' => 'Ana Invitada', 'email' => 'ana@example.com']);
        User::factory()->invitado()->create(['name' => 'Luis Invitado', 'email' => 'luis@example.com']);
        User::factory()->estudiante()->create(['name' => 'Ana Estudiante']);

        $this->get(route('admin.usuarios.index', ['q' => 'ana']))
            ->assertInertia(fn (Assert $page) => $page->has('usuarios', 2));

        $this->get(route('admin.usuarios.index', ['q' => 'ana', 'rol' => 'invitado']))
            ->assertInertia(fn (Assert $page) => $page->has('usuarios', 1)->where('usuarios.0.name', 'Ana Invitada'));
    });

    it('marca como no editables a los estudiantes y al propio administrador', function () {
        $administrador = iniciarComoAdministrador();
        User::factory()->estudiante()->create(['name' => 'Estudiante Uno']);
        User::factory()->invitado()->create(['name' => 'Invitado Uno']);

        $this->get(route('admin.usuarios.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('usuarios', fn ($usuarios) => collect($usuarios)->mapWithKeys(fn ($u) => [$u['name'] => $u['editable']])->sortKeys()->all() === collect([
                    $administrador->name => false,
                    'Estudiante Uno' => false,
                    'Invitado Uno' => true,
                ])->sortKeys()->all()));
    });
});

describe('cambio de rol', function () {
    it('promueve a un invitado a DIGEU', function () {
        iniciarComoAdministrador();
        $invitado = User::factory()->invitado()->create();

        $this->put(route('admin.usuarios.update', $invitado), ['rol' => 'digeu'])->assertRedirect()->assertSessionHasNoErrors();

        expect($invitado->fresh()->esAdministrador())->toBeTrue();
    });

    it('asigna a un invitado el rol de unidad académica junto con su unidad', function () {
        iniciarComoAdministrador();
        $invitado = User::factory()->invitado()->create();
        $unidad = UnidadAcademica::factory()->create();

        $this->put(route('admin.usuarios.update', $invitado), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id])
            ->assertSessionHasNoErrors();

        expect($invitado->fresh()->esUnidadAcademica())->toBeTrue()
            ->and($unidad->fresh()->administrador_id)->toBe($invitado->id)
            ->and($invitado->fresh()->unidadAcademica->id)->toBe($unidad->id);
    });

    it('exige una unidad válida para el rol de unidad académica', function (array $datos, array $errores) {
        iniciarComoAdministrador();
        $invitado = User::factory()->invitado()->create();

        $this->put(route('admin.usuarios.update', $invitado), ['rol' => 'unidad_academica', ...$datos])->assertSessionHasErrors($errores);

        expect($invitado->fresh()->tieneRol(ClaveRol::Invitado))->toBeTrue();
    })->with([
        'sin unidad' => [[], ['unidad_academica_id' => 'Selecciona la unidad académica que administrará.']],
        'unidad inexistente' => [['unidad_academica_id' => 9999], ['unidad_academica_id']],
    ]);

    it('no asigna una unidad que ya administra otra persona', function () {
        iniciarComoAdministrador();
        $titular = User::factory()->invitado()->create(['name' => 'Titular']);
        $unidad = UnidadAcademica::factory()->create();
        $unidad->forceFill(['administrador_id' => $titular->id])->save();
        $otro = User::factory()->invitado()->create();

        $this->put(route('admin.usuarios.update', $otro), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id])
            ->assertSessionHasErrors(['unidad_academica_id' => 'Esa unidad ya la administra Titular. Cámbiale o quítale la unidad primero.']);

        expect($unidad->fresh()->administrador_id)->toBe($titular->id);
    });

    it('permite volver a guardar la misma unidad de quien ya la administra', function () {
        iniciarComoAdministrador();
        $coordinador = User::factory()->invitado()->create();
        $unidad = UnidadAcademica::factory()->create();

        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id]);
        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id])
            ->assertSessionHasNoErrors();
    });

    it('libera la unidad al cambiar de rol o de unidad', function () {
        iniciarComoAdministrador();
        $coordinador = User::factory()->invitado()->create();
        $primera = UnidadAcademica::factory()->create();
        $segunda = UnidadAcademica::factory()->create();
        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'unidad_academica', 'unidad_academica_id' => $primera->id]);

        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'unidad_academica', 'unidad_academica_id' => $segunda->id]);

        expect($primera->fresh()->administrador_id)->toBeNull()
            ->and($segunda->fresh()->administrador_id)->toBe($coordinador->id);

        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'invitado']);

        expect($segunda->fresh()->administrador_id)->toBeNull()
            ->and($coordinador->fresh()->tieneRol(ClaveRol::Invitado))->toBeTrue();
    });

    it('no permite asignar el rol de estudiante ni roles inexistentes', function (string $rol) {
        iniciarComoAdministrador();
        $invitado = User::factory()->invitado()->create();

        $this->put(route('admin.usuarios.update', $invitado), ['rol' => $rol])->assertSessionHasErrors('rol');

        expect($invitado->fresh()->tieneRol(ClaveRol::Invitado))->toBeTrue();
    })->with(['estudiante', 'superadmin', '']);

    it('no permite cambiar el propio acceso', function () {
        $administrador = iniciarComoAdministrador();

        $this->put(route('admin.usuarios.update', $administrador), ['rol' => 'invitado'])->assertForbidden();

        expect($administrador->fresh()->esAdministrador())->toBeTrue();
    });

    it('no permite cambiar el rol de un estudiante', function () {
        iniciarComoAdministrador();
        $estudiante = User::factory()->estudiante()->create();
        Estudiante::factory()->create(['usuario_id' => $estudiante->id]);

        $this->put(route('admin.usuarios.update', $estudiante), ['rol' => 'digeu'])->assertForbidden();

        expect($estudiante->fresh()->esEstudiante())->toBeTrue();
    });
});
