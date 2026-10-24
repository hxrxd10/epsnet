<?php

use App\Enums\ClaveRol;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use App\Notifications\CuentaCreada;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;

function iniciarComoAdministrador(): User
{
    $administrador = User::factory()->administrador()->create(['name' => 'Zoe Administradora', 'email' => 'zoe@digeu.example']);
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
        $this->post(route('admin.usuarios.store'), ['name' => 'X', 'email' => 'x@example.com', 'rol' => 'digeu'])->assertForbidden();

        expect($objetivo->fresh()->tieneRol(ClaveRol::Invitado))->toBeTrue()
            ->and(User::where('email', 'x@example.com')->exists())->toBeFalse();
    })->with(['invitado', 'estudiante']);

    it('niega el acceso a una unidad académica', function () {
        $usuario = User::factory()->create();
        $usuario->forceFill(['rol_id' => Rol::delSistema(ClaveRol::UnidadAcademica)->id])->save();

        $this->actingAs($usuario)->get(route('admin.usuarios.index'))->assertForbidden();
    });
});

describe('listado', function () {
    it('lista a los usuarios con su rol, su unidad y sus carreras', function () {
        iniciarComoAdministrador();
        $unidad = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
        $pedagogia = Carrera::factory()->create(['unidad_academica_id' => $unidad->id, 'nombre' => 'Pedagogía']);
        $coordinador = User::factory()->create(['name' => 'Beatriz Coordinadora']);
        $coordinador->forceFill(['rol_id' => Rol::delSistema(ClaveRol::UnidadAcademica)->id, 'unidad_academica_id' => $unidad->id])->save();
        $coordinador->carreras()->attach($pedagogia);
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
                ->where('usuarios.1.carreras', [['id' => $pedagogia->id, 'nombre' => 'Pedagogía']])
                ->where('unidades.0.carreras', [['value' => (string) $pedagogia->id, 'label' => 'Pedagogía (Plan Diario)']])
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

        expect($invitado->fresh())
            ->esUnidadAcademica()->toBeTrue()
            ->unidad_academica_id->toBe($unidad->id)
            ->and($invitado->fresh()->carreras)->toBeEmpty();
    });

    it('permite varios usuarios en la misma unidad, cada uno con sus carreras', function () {
        iniciarComoAdministrador();
        $unidad = UnidadAcademica::factory()->create();
        [$pedagogia, $derecho] = Carrera::factory()->count(2)->create(['unidad_academica_id' => $unidad->id]);
        $primero = User::factory()->invitado()->create();
        $segundo = User::factory()->invitado()->create();

        $this->put(route('admin.usuarios.update', $primero), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id, 'carreras' => [$pedagogia->id]])
            ->assertSessionHasNoErrors();
        $this->put(route('admin.usuarios.update', $segundo), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id, 'carreras' => [$derecho->id]])
            ->assertSessionHasNoErrors();

        expect($primero->fresh()->carreras->pluck('id')->all())->toBe([$pedagogia->id])
            ->and($segundo->fresh()->carreras->pluck('id')->all())->toBe([$derecho->id])
            ->and($unidad->usuarios()->count())->toBe(2);
    });

    it('exige una unidad válida y carreras de esa unidad para el rol de unidad académica', function (Closure $datos, array $errores) {
        iniciarComoAdministrador();
        $invitado = User::factory()->invitado()->create();

        $this->put(route('admin.usuarios.update', $invitado), ['rol' => 'unidad_academica', ...$datos->call($this)])->assertSessionHasErrors($errores);

        expect($invitado->fresh()->tieneRol(ClaveRol::Invitado))->toBeTrue()
            ->and($invitado->fresh()->carreras)->toBeEmpty();
    })->with([
        'sin unidad' => [fn () => [], ['unidad_academica_id' => 'Selecciona la unidad académica del usuario.']],
        'unidad inexistente' => [fn () => ['unidad_academica_id' => 9999], ['unidad_academica_id']],
        'carrera de otra unidad' => [fn () => [
            'unidad_academica_id' => UnidadAcademica::factory()->create()->id,
            'carreras' => [Carrera::factory()->create()->id],
        ], ['carreras.0' => 'Las carreras deben ser de la unidad académica elegida.']],
    ]);

    it('cambia las carreras y las quita al cambiar de rol', function () {
        iniciarComoAdministrador();
        $unidad = UnidadAcademica::factory()->create();
        [$pedagogia, $derecho] = Carrera::factory()->count(2)->create(['unidad_academica_id' => $unidad->id]);
        $coordinador = User::factory()->invitado()->create();
        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id, 'carreras' => [$pedagogia->id]]);

        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'unidad_academica', 'unidad_academica_id' => $unidad->id, 'carreras' => [$derecho->id]]);

        expect($coordinador->fresh()->carreras->pluck('id')->all())->toBe([$derecho->id]);

        $this->put(route('admin.usuarios.update', $coordinador), ['rol' => 'invitado']);

        expect($coordinador->fresh())
            ->tieneRol(ClaveRol::Invitado)->toBeTrue()
            ->unidad_academica_id->toBeNull()
            ->and($coordinador->fresh()->carreras)->toBeEmpty();
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

describe('creación de cuentas', function () {
    it('crea la cuenta de un supervisor con su unidad y sus carreras y le envía el enlace para definir su contraseña', function () {
        Notification::fake();
        iniciarComoAdministrador();
        $unidad = UnidadAcademica::factory()->create(['nombre' => 'Centro Universitario de Occidente']);
        $carrera = Carrera::factory()->create(['unidad_academica_id' => $unidad->id]);

        $this->post(route('admin.usuarios.store'), [
            'name' => 'Carlos Supervisor',
            'email' => 'carlos@cunoc.example',
            'rol' => 'unidad_academica',
            'unidad_academica_id' => $unidad->id,
            'carreras' => [$carrera->id],
        ])->assertRedirect(route('admin.usuarios.index'))->assertSessionHasNoErrors();

        $usuario = User::where('email', 'carlos@cunoc.example')->sole();

        expect($usuario)
            ->name->toBe('Carlos Supervisor')
            ->esUnidadAcademica()->toBeTrue()
            ->unidad_academica_id->toBe($unidad->id)
            ->email_verified_at->not->toBeNull()
            ->and($usuario->carreras->pluck('id')->all())->toBe([$carrera->id]);

        Notification::assertSentTo($usuario, CuentaCreada::class, function (CuentaCreada $aviso) use ($usuario): bool {
            return $aviso->unidad === 'Centro Universitario de Occidente'
                && Password::broker()->tokenExists($usuario, $aviso->token)
                && str_contains($aviso->toMail($usuario)->actionUrl, route('password.reset', $aviso->token));
        });
    });

    it('valida los datos de la cuenta nueva', function (array $datos, array $errores) {
        Notification::fake();
        iniciarComoAdministrador();
        User::factory()->create(['email' => 'repetido@example.com']);

        $this->post(route('admin.usuarios.store'), ['name' => 'Alguien', 'email' => 'nuevo@example.com', 'rol' => 'digeu', ...$datos])
            ->assertSessionHasErrors($errores);

        Notification::assertNothingSent();
        expect(User::where('email', 'nuevo@example.com')->exists())->toBeFalse();
    })->with([
        'sin nombre' => [['name' => ''], ['name' => 'Escribe el nombre de la persona.']],
        'correo repetido' => [['email' => 'repetido@example.com'], ['email' => 'Ya existe una cuenta con este correo. Búscala en la lista y edita su acceso.']],
        'rol de estudiante' => [['rol' => 'estudiante'], ['rol']],
        'unidad académica sin unidad' => [['rol' => 'unidad_academica'], ['unidad_academica_id']],
    ]);
});
