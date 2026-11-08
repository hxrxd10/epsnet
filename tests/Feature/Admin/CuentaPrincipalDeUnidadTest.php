<?php

use App\Enums\ClaveRol;
use App\Models\Carrera;
use App\Models\UnidadAcademica;
use App\Models\User;
use App\Notifications\CuentaCreada;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
    $this->unidad = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
    $this->otraUnidad = UnidadAcademica::factory()->create(['nombre' => 'Centro Universitario de Occidente']);
    $this->pedagogia = Carrera::factory()->create(['unidad_academica_id' => $this->unidad->id, 'nombre' => 'Pedagogía']);
    $this->ajena = Carrera::factory()->create(['unidad_academica_id' => $this->otraUnidad->id, 'nombre' => 'Agronomía']);
});

/**
 * Cuenta principal de la unidad: rol de unidad académica, con unidad y sin carreras asignadas.
 */
function iniciarComoPrincipal(UnidadAcademica $unidad): User
{
    $principal = User::factory()->unidadAcademica($unidad)->create(['name' => 'Paula Principal']);
    test()->actingAs($principal);

    return $principal;
}

function supervisorDe(Carrera $carrera, array $atributos = []): User
{
    $supervisor = User::factory()->unidadAcademica($carrera->unidadAcademica)->create($atributos);
    $supervisor->carreras()->attach($carrera);

    return $supervisor;
}

describe('acceso', function () {
    it('da a la cuenta principal el menú de supervisores y carreras', function () {
        iniciarComoPrincipal($this->unidad);

        $this->get(route('admin.usuarios.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('gestionaSupervisores', true));
        $this->get(route('admin.carreras.index'))->assertOk();
    });

    it('niega supervisores y carreras a un supervisor con carreras y a una cuenta sin unidad', function (Closure $crear) {
        $this->actingAs($crear($this->pedagogia));

        $this->get(route('admin.usuarios.index'))->assertForbidden();
        $this->post(route('admin.usuarios.store'), ['name' => 'X', 'email' => 'x@example.com', 'carreras' => [$this->pedagogia->id]])->assertForbidden();
        $this->get(route('admin.carreras.index'))->assertForbidden();

        expect(User::where('email', 'x@example.com')->exists())->toBeFalse();
    })->with([
        'supervisor' => [fn (Carrera $carrera) => supervisorDe($carrera)],
        'sin unidad' => [fn () => User::factory()->unidadAcademica()->create()],
    ]);

    it('deja el resto del manejo de datos solo a DIGEU', function () {
        iniciarComoPrincipal($this->unidad);

        $this->get(route('admin.datos'))->assertForbidden();
        $this->get(route('admin.bitacora.index'))->assertForbidden();
        $this->delete(route('admin.carreras.destroy', $this->pedagogia))->assertForbidden();

        expect($this->pedagogia->fresh())->not->toBeNull();
    });
});

describe('supervisores', function () {
    it('lista solo las cuentas de unidad académica de su unidad', function () {
        iniciarComoPrincipal($this->unidad);
        supervisorDe($this->pedagogia, ['name' => 'Beto Supervisor']);
        supervisorDe($this->ajena, ['name' => 'Otra Unidad']);
        User::factory()->invitado()->create(['name' => 'Ana Invitada']);
        User::factory()->administrador()->create(['name' => 'Zoe DIGEU']);

        $this->get(route('admin.usuarios.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('usuarios', fn ($usuarios) => collect($usuarios)->pluck('name')->all() === ['Beto Supervisor', 'Paula Principal'])
                ->where('usuarios.0.editable', true)
                ->where('usuarios.1.editable', false)
                ->where('usuarios.1.correo_editable', false)
                ->where('unidadPropia', ['id' => $this->unidad->id, 'nombre' => 'Facultad de Humanidades'])
                ->where('rolesAsignables', [['value' => 'unidad_academica', 'label' => ClaveRol::UnidadAcademica->etiqueta()]])
                ->where('unidades', fn ($unidades) => collect($unidades)->pluck('value')->all() === [(string) $this->unidad->id]));
    });

    it('crea un supervisor de su unidad aunque el navegador pida otro rol u otra unidad', function () {
        Notification::fake();
        iniciarComoPrincipal($this->unidad);

        $this->post(route('admin.usuarios.store'), [
            'name' => 'Beto Supervisor',
            'email' => 'beto@example.com',
            'rol' => 'digeu',
            'unidad_academica_id' => $this->otraUnidad->id,
            'carreras' => [$this->pedagogia->id],
        ])->assertRedirect(route('admin.usuarios.index'));

        $supervisor = User::where('email', 'beto@example.com')->sole();

        expect($supervisor->tieneRol(ClaveRol::UnidadAcademica))->toBeTrue()
            ->and($supervisor->unidad_academica_id)->toBe($this->unidad->id)
            ->and($supervisor->carreras->pluck('id')->all())->toBe([$this->pedagogia->id]);
        Notification::assertSentTo($supervisor, CuentaCreada::class);
    });

    it('exige al menos una carrera y que sean de su unidad', function (Closure $carreras, string $error) {
        iniciarComoPrincipal($this->unidad);

        $this->post(route('admin.usuarios.store'), ['name' => 'Beto', 'email' => 'beto@example.com', 'carreras' => $carreras($this)])
            ->assertSessionHasErrors($error);

        expect(User::where('email', 'beto@example.com')->exists())->toBeFalse();
    })->with([
        'sin carreras' => [fn () => [], 'carreras'],
        'carrera de otra unidad' => [fn ($prueba) => [$prueba->ajena->id], 'carreras.0'],
    ]);

    it('cambia las carreras y el correo de un supervisor de su unidad', function () {
        Notification::fake();
        iniciarComoPrincipal($this->unidad);
        $historia = Carrera::factory()->create(['unidad_academica_id' => $this->unidad->id]);
        $supervisor = supervisorDe($this->pedagogia);

        $this->put(route('admin.usuarios.update', $supervisor), ['carreras' => [$historia->id]])->assertSessionHasNoErrors();
        $this->put(route('admin.usuarios.correo', $supervisor), ['email' => 'nuevo@example.com'])->assertSessionHasNoErrors();

        expect($supervisor->fresh()->carreras->pluck('id')->all())->toBe([$historia->id])
            ->and($supervisor->fresh()->email)->toBe('nuevo@example.com');
    });

    it('no toca cuentas ajenas: otra unidad, otra cuenta principal, invitados ni la suya', function (Closure $crear) {
        $principal = iniciarComoPrincipal($this->unidad);
        $cuenta = $crear($this, $principal);
        $correo = $cuenta->email;

        $this->put(route('admin.usuarios.update', $cuenta), ['carreras' => [$this->pedagogia->id]])->assertForbidden();
        $this->put(route('admin.usuarios.correo', $cuenta), ['email' => 'otro@example.com'])->assertForbidden();

        expect($cuenta->fresh()->email)->toBe($correo)
            ->and($cuenta->fresh()->carreras->contains($this->pedagogia))->toBeFalse();
    })->with([
        'supervisor de otra unidad' => [fn ($prueba) => supervisorDe($prueba->ajena)],
        'otra cuenta principal' => [fn ($prueba) => User::factory()->unidadAcademica($prueba->unidad)->create()],
        'invitado' => [fn () => User::factory()->invitado()->create()],
        'la suya' => [fn ($prueba, User $principal) => $principal],
    ]);

    it('deja de ser cuenta principal cuando DIGEU le asigna carreras', function () {
        $principal = iniciarComoPrincipal($this->unidad);
        $principal->carreras()->attach($this->pedagogia);

        $this->get(route('admin.usuarios.index'))->assertForbidden();
    });
});

describe('carreras', function () {
    it('lista solo las carreras de su unidad y no permite eliminarlas', function () {
        iniciarComoPrincipal($this->unidad);

        $this->get(route('admin.carreras.index', ['unidad' => $this->otraUnidad->id]))
            ->assertInertia(fn (Assert $page) => $page
                ->has('carreras', 1)
                ->where('carreras.0.nombre', 'Pedagogía')
                ->where('unidadPropia', 'Facultad de Humanidades')
                ->where('puedeEliminar', false)
                ->where('unidades', []));
    });

    it('al consultar un carné solo muestra las carreras de su unidad', function () {
        RegistroAcademicoFalso::responder(Str::replaceLast('Facultad de Humanidades', 'Centro Universitario de Occidente', RegistroAcademicoFalso::xml()));
        iniciarComoPrincipal($this->unidad);

        $this->get(route('admin.carreras.index', ['carnet' => RegistroAcademicoFalso::CARNET]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('consulta.carreras', fn ($carreras) => collect($carreras)->pluck('clave')->all() === ['07-00-28'])
                ->where('consulta.nuevas', 1));
    });

    it('agrega carreras de su unidad pero no de otras', function () {
        RegistroAcademicoFalso::responder(Str::replaceLast('Facultad de Humanidades', 'Centro Universitario de Occidente', RegistroAcademicoFalso::xml()));
        iniciarComoPrincipal($this->unidad);

        $this->post(route('admin.carreras.store'), ['carnet' => RegistroAcademicoFalso::CARNET, 'claves' => ['07-00-28', '77-00-66']])
            ->assertSessionHasErrors(['claves' => 'Solo puedes agregar carreras de Facultad de Humanidades.']);
        expect(Carrera::where('codigo_carrera', '28')->exists())->toBeFalse();

        $this->post(route('admin.carreras.store'), ['carnet' => RegistroAcademicoFalso::CARNET, 'claves' => ['07-00-28']])
            ->assertSessionHasNoErrors();
        expect(Carrera::where('codigo_unidad', '07')->where('codigo_carrera', '28')->sole()->unidad_academica_id)->toBe($this->unidad->id)
            ->and(Carrera::where('codigo_unidad', '77')->exists())->toBeFalse();
    });
});
