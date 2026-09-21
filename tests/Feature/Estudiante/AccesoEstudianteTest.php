<?php

use App\Enums\ClaveRol;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

function verificarIdentidad(array $datos = []): TestResponse
{
    return test()->post(route('estudiante.acceso.store'), array_merge([
        'registro_academico' => RegistroAcademicoFalso::CARNET,
        'dpi' => RegistroAcademicoFalso::CUI,
    ], $datos));
}

function crearCuenta(array $datos = []): TestResponse
{
    return test()->post(route('estudiante.cuenta.store'), array_merge([
        'email' => 'madeley@example.com',
        'password' => 'contraseña-segura-1',
        'password_confirmation' => 'contraseña-segura-1',
    ], $datos));
}

describe('verificación de identidad', function () {
    it('muestra el formulario a los invitados', function () {
        $this->get(route('estudiante.acceso'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('estudiante/acceso'));
    });

    it('lleva a crear la cuenta a quien ingresa un DPI que coincide con su CUI', function () {
        RegistroAcademicoFalso::responder();

        verificarIdentidad()->assertRedirect(route('estudiante.cuenta.create'));

        $estudiante = Estudiante::firstWhere('carnet', RegistroAcademicoFalso::CARNET);

        expect($estudiante)->not->toBeNull()
            ->nombre1->toBe('Madeley')
            ->apellido1->toBe('Morales')
            ->apellido2->toBe('Vivar')
            ->nacionalidad->toBe('Guatemalteca')
            ->usuario_id->toBeNull()
            ->ultima_consulta_at->not->toBeNull()
            ->and($estudiante->carreras)->toHaveCount(2)
            ->and($estudiante->carreras[1]['nombreCarrera'])->toBe('Licenciatura en Pedagogía y Administración Educativa');

        $this->assertGuest();
        expect(session('estudiante.verificado.estudiante_id'))->toBe($estudiante->id);
    });

    it('no guarda el CUI del estudiante', function () {
        RegistroAcademicoFalso::responder();

        verificarIdentidad();

        expect(json_encode(Estudiante::first()->getAttributes()))->not->toContain(RegistroAcademicoFalso::CUI)
            ->and(json_encode(session()->all()))->not->toContain(RegistroAcademicoFalso::CUI);
    });

    it('acepta el registro académico y el DPI con espacios o guiones', function () {
        RegistroAcademicoFalso::responder();

        verificarIdentidad(['registro_academico' => '2012 19511', 'dpi' => '2165-91457-0101'])
            ->assertRedirect(route('estudiante.cuenta.create'));
    });

    it('actualiza al estudiante existente en lugar de duplicarlo', function () {
        RegistroAcademicoFalso::responder();
        Estudiante::factory()->create(['carnet' => RegistroAcademicoFalso::CARNET, 'nombre1' => 'Nombre desactualizado']);

        verificarIdentidad();

        expect(Estudiante::count())->toBe(1)
            ->and(Estudiante::first()->nombre1)->toBe('Madeley');
    });

    it('envía a iniciar sesión a quien ya activó su cuenta', function () {
        RegistroAcademicoFalso::responder();
        $usuario = User::factory()->estudiante()->create();
        Estudiante::factory()->create(['carnet' => RegistroAcademicoFalso::CARNET, 'usuario_id' => $usuario->id]);

        verificarIdentidad()
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Tu cuenta ya está activada. Inicia sesión con tu correo y tu contraseña.');

        $this->assertGuest();
        expect(session('estudiante.verificado'))->toBeNull();
    });

    it('rechaza un DPI que no coincide con el CUI', function () {
        RegistroAcademicoFalso::responder();

        verificarIdentidad(['dpi' => '1234567890123'])
            ->assertSessionHasErrors(['acceso' => 'El registro académico y el DPI no coinciden con los datos de la USAC.']);

        expect(Estudiante::count())->toBe(0)
            ->and(session('estudiante.verificado'))->toBeNull();
    });

    it('rechaza un registro académico que el servicio no conoce', function () {
        RegistroAcademicoFalso::responder('<RESP_CONSULTA_DATOS></RESP_CONSULTA_DATOS>');

        verificarIdentidad()->assertSessionHasErrors(['acceso' => 'El registro académico y el DPI no coinciden con los datos de la USAC.']);
    });

    it('informa que el servicio no está disponible sin dejar avanzar', function () {
        RegistroAcademicoFalso::fallar();

        verificarIdentidad()->assertSessionHasErrors(['acceso' => 'No pudimos consultar el registro académico en este momento. Intenta de nuevo en unos minutos.']);

        expect(session('estudiante.verificado'))->toBeNull();
    });

    it('valida el formato del registro académico y del DPI sin consultar el servicio', function (array $datos, array $errores) {
        verificarIdentidad($datos)->assertSessionHasErrors($errores);

        expect(RegistroAcademicoFalso::solicitudes())->toBeEmpty();
    })->with([
        'vacíos' => [['registro_academico' => '', 'dpi' => ''], [
            'registro_academico' => 'Ingresa el registro académico.',
            'dpi' => 'Ingresa el DPI.',
        ]],
        'registro académico muy corto' => [['registro_academico' => '123'], [
            'registro_academico' => 'El registro académico solo debe contener números (entre 6 y 12 dígitos).',
        ]],
        'DPI incompleto' => [['dpi' => '12345'], ['dpi' => 'El DPI debe tener 13 dígitos.']],
    ]);

    it('limita los intentos repetidos para el mismo carné', function () {
        RegistroAcademicoFalso::responder();

        foreach (range(1, 5) as $intento) {
            verificarIdentidad(['dpi' => '1234567890123'])->assertSessionHasErrors('acceso');
        }

        verificarIdentidad(['dpi' => '1234567890123'])->assertTooManyRequests();
        verificarIdentidad()->assertTooManyRequests();
    });

    it('lleva al estudiante con sesión directo a sus carreras', function () {
        RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());

        $this->get(route('estudiante.acceso'))->assertRedirect(route('estudiante.carreras'));
    });
});

describe('invitados que pasan a estudiante', function () {
    it('muestra la verificación a un invitado con sesión', function () {
        $this->actingAs(User::factory()->invitado()->create())
            ->get(route('estudiante.acceso'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('estudiante/acceso')
                ->where('esInvitado', true));
    });

    it('convierte al invitado en estudiante al verificar su identidad', function () {
        RegistroAcademicoFalso::responder();
        $usuario = User::factory()->invitado()->create();
        $this->actingAs($usuario);

        verificarIdentidad()->assertRedirect(route('estudiante.carreras'));

        $estudiante = Estudiante::firstWhere('carnet', RegistroAcademicoFalso::CARNET);

        expect($estudiante->usuario_id)->toBe($usuario->id)
            ->and($usuario->fresh()->esEstudiante())->toBeTrue()
            ->and(User::count())->toBe(1);
    });

    it('deja al invitado como está si el DPI no coincide', function () {
        RegistroAcademicoFalso::responder();
        $usuario = User::factory()->invitado()->create();
        $this->actingAs($usuario);

        verificarIdentidad(['dpi' => '1234567890123'])->assertSessionHasErrors('acceso');

        expect($usuario->fresh()->esEstudiante())->toBeFalse()
            ->and(Estudiante::count())->toBe(0);
    });

    it('no permite vincular un registro académico que ya tiene otra cuenta', function () {
        RegistroAcademicoFalso::responder();
        $otro = User::factory()->estudiante()->create();
        Estudiante::factory()->create(['carnet' => RegistroAcademicoFalso::CARNET, 'usuario_id' => $otro->id]);
        $usuario = User::factory()->invitado()->create();
        $this->actingAs($usuario);

        verificarIdentidad()->assertSessionHasErrors(['acceso' => 'Este registro académico ya está vinculado a otra cuenta.']);

        expect($usuario->fresh()->esEstudiante())->toBeFalse();
    });

    it('no permite verificar la identidad a cuentas de administradores', function () {
        $this->actingAs(User::factory()->administrador()->create());

        $this->get(route('estudiante.acceso'))->assertForbidden();
        verificarIdentidad()->assertForbidden();
    });
});

describe('creación de la cuenta', function () {
    it('pide verificar la identidad antes de crear la cuenta', function () {
        $this->get(route('estudiante.cuenta.create'))->assertRedirect(route('estudiante.acceso'));

        crearCuenta()->assertRedirect(route('estudiante.acceso'));

        expect(User::count())->toBe(0);
    });

    it('muestra el formulario al estudiante verificado', function () {
        RegistroAcademicoFalso::responder();
        verificarIdentidad();

        $this->get(route('estudiante.cuenta.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('estudiante/cuenta')
                ->where('perfil.carnet', RegistroAcademicoFalso::CARNET)
                ->where('perfil.nombre_completo', 'Madeley Morales Vivar')
                ->where('estudiante', null));
    });

    it('crea el usuario con rol de estudiante, lo vincula a su perfil e inicia sesión', function () {
        RegistroAcademicoFalso::responder();
        verificarIdentidad();

        crearCuenta(['email' => '  Madeley@Example.COM '])->assertRedirect(route('estudiante.carreras'));

        $usuario = User::firstWhere('email', 'madeley@example.com');
        $estudiante = Estudiante::firstWhere('carnet', RegistroAcademicoFalso::CARNET);

        expect($usuario)->not->toBeNull()
            ->name->toBe('Madeley Morales Vivar')
            ->activo->toBeTrue()
            ->and($usuario->tieneRol(ClaveRol::Estudiante))->toBeTrue()
            ->and(Hash::check('contraseña-segura-1', $usuario->password))->toBeTrue()
            ->and($estudiante->usuario_id)->toBe($usuario->id)
            ->and(session('estudiante.verificado'))->toBeNull();

        $this->assertAuthenticatedAs($usuario);
    });

    it('rechaza correos repetidos o inválidos y contraseñas que no coinciden', function (array $datos, array $errores) {
        RegistroAcademicoFalso::responder();
        verificarIdentidad();
        User::factory()->create(['email' => 'ocupado@example.com']);

        crearCuenta($datos)->assertSessionHasErrors($errores);

        $this->assertGuest();
        expect(Estudiante::first()->usuario_id)->toBeNull();
    })->with([
        'correo ocupado' => [['email' => 'ocupado@example.com'], ['email' => 'Ya existe una cuenta con este correo.']],
        'correo inválido' => [['email' => 'no-es-correo'], ['email' => 'El correo electrónico debe ser un correo electrónico válido.']],
        'contraseña sin confirmar' => [['password_confirmation' => 'otra'], ['password' => 'La confirmación de la contraseña no coincide.']],
        'contraseña muy corta' => [['password' => 'corta', 'password_confirmation' => 'corta'], ['password']],
    ]);

    it('vence la verificación pasados 30 minutos', function () {
        RegistroAcademicoFalso::responder();
        verificarIdentidad();

        $this->travel(31)->minutes();

        $this->get(route('estudiante.cuenta.create'))->assertRedirect(route('estudiante.acceso'));
        crearCuenta()->assertRedirect(route('estudiante.acceso'));

        expect(User::count())->toBe(0);
    });

    it('no crea una segunda cuenta si el perfil ya fue vinculado', function () {
        RegistroAcademicoFalso::responder();
        verificarIdentidad();
        Estudiante::first()->forceFill(['usuario_id' => User::factory()->estudiante()->create()->id])->save();

        crearCuenta()->assertRedirect(route('estudiante.acceso'));

        expect(User::count())->toBe(1);
    });
});

describe('inicio de sesión y rol de estudiante', function () {
    it('lleva a los estudiantes a sus carreras al iniciar sesión', function () {
        $usuario = User::factory()->estudiante()->create();
        Estudiante::factory()->create(['usuario_id' => $usuario->id]);

        $this->post(route('login.store'), ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect(route('estudiante.carreras'));
    });

    it('lleva al panel a quien no es estudiante', function () {
        $usuario = User::factory()->create();

        $this->post(route('login.store'), ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
    });

    it('envía a los invitados a iniciar sesión', function () {
        $this->get(route('estudiante.carreras'))->assertRedirect(route('login'));
    });

    it('niega el acceso a usuarios sin el rol de estudiante', function (?ClaveRol $rol) {
        $usuario = User::factory()->create();

        if ($rol !== null) {
            $usuario->forceFill(['rol_id' => Rol::delSistema($rol)->id])->save();
        }

        $this->actingAs($usuario)->get(route('estudiante.carreras'))->assertForbidden();
    })->with([
        'sin rol' => [null],
        'DIGEU' => [ClaveRol::Digeu],
        'unidad académica' => [ClaveRol::UnidadAcademica],
    ]);

    it('niega el acceso a un estudiante sin perfil', function () {
        $this->actingAs(User::factory()->estudiante()->create())
            ->get(route('estudiante.carreras'))
            ->assertForbidden();
    });

    it('cierra la sesión del estudiante', function () {
        RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());

        $this->post(route('logout'))->assertRedirect();

        $this->assertGuest();
    });
});
