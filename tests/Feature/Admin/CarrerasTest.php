<?php

use App\Models\Carrera;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

function administradorDeCarreras(): User
{
    $administrador = User::factory()->administrador()->create();
    test()->actingAs($administrador);

    return $administrador;
}

describe('acceso', function () {
    it('es solo para DIGEU', function (string $rol) {
        $this->actingAs(User::factory()->{$rol}()->create());
        $carrera = Carrera::factory()->create();

        $this->get(route('admin.carreras.index'))->assertForbidden();
        $this->post(route('admin.carreras.store'), ['carnet' => RegistroAcademicoFalso::CARNET, 'claves' => ['07-00-28']])->assertForbidden();
        $this->delete(route('admin.carreras.destroy', $carrera))->assertForbidden();

        expect(Carrera::count())->toBe(1)
            ->and(RegistroAcademicoFalso::solicitudes())->toBe([]);
    })->with(['invitado', 'estudiante']);
});

describe('consulta por carné', function () {
    it('muestra las carreras del carné e indica cuáles ya están agregadas', function () {
        administradorDeCarreras();
        Carrera::factory()->create(['codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '28']);

        $this->get(route('admin.carreras.index', ['carnet' => RegistroAcademicoFalso::CARNET]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/carreras')
                ->where('consulta.carnet', RegistroAcademicoFalso::CARNET)
                ->where('consulta.error', null)
                ->where('consulta.nuevas', 1)
                ->where('consulta.carreras.0.clave', '07-00-28')
                ->where('consulta.carreras.0.agregada', true)
                ->where('consulta.carreras.1.clave', '77-00-66')
                ->where('consulta.carreras.1.nombre', 'Licenciatura en Pedagogía y Administración Educativa')
                ->where('consulta.carreras.1.agregada', false));
    });

    it('avisa cuando el carné no existe o el servicio no responde', function (Closure $preparar, string $error) {
        administradorDeCarreras();
        $preparar();

        $this->get(route('admin.carreras.index', ['carnet' => '200000001']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('consulta.error', $error)->where('consulta.carreras', []));
    })->with([
        'carné desconocido' => [fn () => RegistroAcademicoFalso::responder(RegistroAcademicoFalso::xml('999999999')), 'El registro académico no tiene información de ese carné.'],
        'servicio caído' => [fn () => RegistroAcademicoFalso::fallar(), 'El registro académico no responde en este momento. Intenta de nuevo en unos minutos.'],
    ]);

    it('no consulta el servicio sin carné', function () {
        administradorDeCarreras();

        $this->get(route('admin.carreras.index'))->assertInertia(fn (Assert $page) => $page->where('consulta', null));

        expect(RegistroAcademicoFalso::solicitudes())->toBe([]);
    });
});

describe('agregar carreras', function () {
    it('agrega las carreras elegidas con los datos del registro académico y su unidad', function () {
        administradorDeCarreras();

        $this->post(route('admin.carreras.store'), ['carnet' => RegistroAcademicoFalso::CARNET, 'claves' => ['77-00-66']])
            ->assertRedirect(route('admin.carreras.index', ['carnet' => RegistroAcademicoFalso::CARNET]))
            ->assertInertiaFlash('toast.message', 'Carrera agregada.');

        expect(Carrera::sole())
            ->clave()->toBe('77-00-66')
            ->nombre->toBe('Licenciatura en Pedagogía y Administración Educativa')
            ->and(Carrera::sole()->unidadAcademica->nombre)->toBe('Facultad de Humanidades');
    });

    it('no duplica las que ya estaban agregadas', function () {
        administradorDeCarreras();
        $existente = Carrera::factory()->create(['codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '28', 'nombre' => 'Nombre anterior']);

        $this->post(route('admin.carreras.store'), ['carnet' => RegistroAcademicoFalso::CARNET, 'claves' => ['07-00-28', '77-00-66']])
            ->assertInertiaFlash('toast.message', 'Carrera agregada.');

        expect(Carrera::count())->toBe(2)
            ->and($existente->fresh()->nombre)->toBe('Nombre anterior');
    });

    it('rechaza claves que no son de ese carné, sin confiar en lo que manda el navegador', function () {
        administradorDeCarreras();

        $this->post(route('admin.carreras.store'), ['carnet' => RegistroAcademicoFalso::CARNET, 'claves' => ['99-99-99']])
            ->assertSessionHasErrors(['claves' => 'Las carreras elegidas no son de ese carné.']);

        expect(Carrera::count())->toBe(0);
    });

    it('valida el carné y que se elija al menos una carrera', function (array $datos, string $campo) {
        administradorDeCarreras();

        $this->post(route('admin.carreras.store'), $datos)->assertSessionHasErrors($campo);

        expect(Carrera::count())->toBe(0);
    })->with([
        'sin carné' => [['claves' => ['77-00-66']], 'carnet'],
        'carné con letras' => [['carnet' => 'abc', 'claves' => ['77-00-66']], 'carnet'],
        'sin carreras' => [['carnet' => RegistroAcademicoFalso::CARNET, 'claves' => []], 'claves'],
    ]);
});

describe('eliminar', function () {
    it('elimina una carrera sin supervisores', function () {
        administradorDeCarreras();
        $carrera = Carrera::factory()->create();

        $this->delete(route('admin.carreras.destroy', $carrera))->assertRedirect(route('admin.carreras.index'));

        expect(Carrera::count())->toBe(0);
    });

    it('no elimina una carrera asignada a un supervisor, para que no pase a ver toda su unidad', function () {
        administradorDeCarreras();
        $carrera = Carrera::factory()->create();
        User::factory()->create()->carreras()->attach($carrera);

        $this->delete(route('admin.carreras.destroy', $carrera))->assertInertiaFlash('toast.type', 'error');

        expect($carrera->fresh())->not->toBeNull();
    });
});

it('lista las carreras con su unidad y filtra por unidad', function () {
    administradorDeCarreras();
    $humanidades = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
    Carrera::factory()->create(['unidad_academica_id' => $humanidades->id, 'nombre' => 'Pedagogía']);
    Carrera::factory()->create(['nombre' => 'Agronomía']);

    $this->get(route('admin.carreras.index', ['unidad' => $humanidades->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->has('carreras', 1)
            ->where('carreras.0.nombre', 'Pedagogía')
            ->where('carreras.0.unidad', 'Facultad de Humanidades'));
});
