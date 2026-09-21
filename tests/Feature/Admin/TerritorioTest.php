<?php

use App\Models\Departamento;
use App\Models\Expediente;
use App\Models\Municipio;
use App\Models\UbicacionTerritorial;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function administradorTerritorio(): User
{
    $usuario = User::factory()->administrador()->create();
    test()->actingAs($usuario);

    return $usuario;
}

describe('acceso', function () {
    it('envía a los invitados a iniciar sesión', function () {
        $this->get(route('admin.municipios.index'))->assertRedirect(route('login'));
        $this->get(route('admin.departamentos.index'))->assertRedirect(route('login'));
    });

    it('niega el acceso a quien no es administrador', function (string $estado) {
        $municipio = Municipio::factory()->create();
        $this->actingAs(User::factory()->{$estado}()->create());

        $this->get(route('admin.municipios.index'))->assertForbidden();
        $this->get(route('admin.departamentos.index'))->assertForbidden();
        $this->post(route('admin.municipios.store'), [])->assertForbidden();
        $this->put(route('admin.municipios.update', $municipio), [])->assertForbidden();
        $this->delete(route('admin.municipios.destroy', $municipio))->assertForbidden();
        $this->delete(route('admin.departamentos.destroy', $municipio->departamento))->assertForbidden();

        expect(Municipio::count())->toBe(1);
    })->with(['invitado', 'estudiante']);
});

describe('municipios', function () {
    it('lista los municipios con su departamento y si están en uso', function () {
        administradorTerritorio();
        $sacatepequez = Departamento::factory()->create(['codigo' => '03', 'nombre' => 'Sacatepéquez']);
        $antigua = Municipio::factory()->for($sacatepequez)->create(['codigo' => '0301', 'nombre' => 'Antigua Guatemala']);
        Municipio::factory()->for($sacatepequez)->create(['codigo' => '0302', 'nombre' => 'Jocotenango']);
        UbicacionTerritorial::factory()->create(['municipio_id' => $antigua->id]);

        $this->get(route('admin.municipios.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/municipios')
                ->where('pagina.total', 2)
                ->where('municipios.0.codigo', '0301')
                ->where('municipios.0.departamento', 'Sacatepéquez')
                ->where('municipios.0.en_uso', true)
                ->where('municipios.1.en_uso', false));
    });

    it('filtra por texto y por departamento', function () {
        administradorTerritorio();
        $sacatepequez = Departamento::factory()->create(['codigo' => '03']);
        $peten = Departamento::factory()->create(['codigo' => '17']);
        Municipio::factory()->for($sacatepequez)->create(['codigo' => '0301', 'nombre' => 'Antigua Guatemala']);
        Municipio::factory()->for($sacatepequez)->create(['codigo' => '0302', 'nombre' => 'Jocotenango']);
        Municipio::factory()->for($peten)->create(['codigo' => '1701', 'nombre' => 'Flores']);

        $this->get(route('admin.municipios.index', ['q' => 'joco']))
            ->assertInertia(fn (Assert $page) => $page->has('municipios', 1)->where('municipios.0.nombre', 'Jocotenango'));
        $this->get(route('admin.municipios.index', ['q' => '1701']))
            ->assertInertia(fn (Assert $page) => $page->has('municipios', 1)->where('municipios.0.nombre', 'Flores'));
        $this->get(route('admin.municipios.index', ['departamento' => $sacatepequez->id]))
            ->assertInertia(fn (Assert $page) => $page->has('municipios', 2)->where('filtros.departamento', (string) $sacatepequez->id));
    });

    it('pagina de 20 en 20', function () {
        administradorTerritorio();
        Municipio::factory()->count(25)->create();

        $this->get(route('admin.municipios.index'))
            ->assertInertia(fn (Assert $page) => $page->has('municipios', 20)->where('pagina.ultima', 2)->where('pagina.total', 25));
    });

    it('agrega un municipio con sus coordenadas', function () {
        administradorTerritorio();
        $departamento = Departamento::factory()->create(['codigo' => '03']);

        $this->post(route('admin.municipios.store'), [
            'departamento_id' => $departamento->id,
            'codigo' => '0301',
            'nombre' => 'Antigua Guatemala',
            'latitud' => 14.5586,
            'longitud' => -90.7295,
        ])->assertSessionHasNoErrors();

        expect(Municipio::sole())
            ->departamento_id->toBe($departamento->id)
            ->codigo->toBe('0301')
            ->nombre->toBe('Antigua Guatemala')
            ->latitud->toBe('14.5586000');
    });

    it('permite dejar el municipio sin coordenadas', function () {
        administradorTerritorio();
        $departamento = Departamento::factory()->create(['codigo' => '03']);

        $this->post(route('admin.municipios.store'), ['departamento_id' => $departamento->id, 'codigo' => '0301', 'nombre' => 'Antigua Guatemala'])
            ->assertSessionHasNoErrors();

        expect(Municipio::sole()->latitud)->toBeNull();
    });

    it('valida el municipio', function (array $cambios, array $errores) {
        administradorTerritorio();
        $sacatepequez = Departamento::factory()->create(['codigo' => '03']);
        Departamento::factory()->create(['codigo' => '17']);
        Municipio::factory()->for($sacatepequez)->create(['codigo' => '0301', 'nombre' => 'Antigua Guatemala']);

        $this->post(route('admin.municipios.store'), [
            'departamento_id' => $sacatepequez->id,
            'codigo' => '0302',
            'nombre' => 'Jocotenango',
            ...$cambios,
        ])->assertSessionHasErrors($errores);

        expect(Municipio::count())->toBe(1);
    })->with([
        'sin nombre' => [['nombre' => ''], ['nombre' => 'Ingresa el nombre.']],
        'código repetido' => [['codigo' => '0301'], ['codigo' => 'Ya existe un municipio con este código.']],
        'código con formato inválido' => [['codigo' => '30'], ['codigo' => 'El código son cuatro dígitos: el del departamento y el del municipio, por ejemplo 0101.']],
        'código de otro departamento' => [['codigo' => '1701'], ['codigo' => 'El código debe empezar con el del departamento (03).']],
        'nombre repetido en el departamento' => [['nombre' => 'Antigua Guatemala'], ['nombre' => 'Este departamento ya tiene un municipio con ese nombre.']],
        'departamento inexistente' => [['departamento_id' => 9999], ['departamento_id']],
        'latitud sin longitud' => [['latitud' => 14.5], ['longitud' => 'Ingresa la longitud junto con la latitud.']],
        'latitud fuera del país' => [['latitud' => 40.7, 'longitud' => -90.7], ['latitud' => 'La latitud debe estar dentro del territorio de Guatemala (entre 13.5 y 18.5).']],
        'longitud fuera del país' => [['latitud' => 14.5, 'longitud' => -74.0], ['longitud' => 'La longitud debe estar dentro del territorio de Guatemala (entre -92.5 y -88).']],
    ]);

    it('permite el mismo nombre en otro departamento', function () {
        administradorTerritorio();
        Municipio::factory()->for(Departamento::factory()->create(['codigo' => '01']))->create(['codigo' => '0101', 'nombre' => 'San José']);
        $otro = Departamento::factory()->create(['codigo' => '05']);

        $this->post(route('admin.municipios.store'), ['departamento_id' => $otro->id, 'codigo' => '0509', 'nombre' => 'San José'])
            ->assertSessionHasNoErrors();

        expect(Municipio::count())->toBe(2);
    });

    it('actualiza un municipio conservando su propio código y nombre', function () {
        administradorTerritorio();
        $departamento = Departamento::factory()->create(['codigo' => '03']);
        $municipio = Municipio::factory()->for($departamento)->create(['codigo' => '0301', 'nombre' => 'Antigua']);

        $this->put(route('admin.municipios.update', $municipio), [
            'departamento_id' => $departamento->id,
            'codigo' => '0301',
            'nombre' => 'Antigua Guatemala',
            'latitud' => 14.56,
            'longitud' => -90.73,
        ])->assertSessionHasNoErrors();

        expect($municipio->fresh())->nombre->toBe('Antigua Guatemala')->latitud->toBe('14.5600000');
    });

    it('elimina un municipio sin EPS', function () {
        administradorTerritorio();
        $municipio = Municipio::factory()->create();

        $this->delete(route('admin.municipios.destroy', $municipio))->assertRedirect();

        expect(Municipio::count())->toBe(0);
    });

    it('no elimina un municipio donde hay EPS, ni siquiera eliminados', function (bool $eliminada) {
        administradorTerritorio();
        $municipio = Municipio::factory()->create();
        $ubicacion = UbicacionTerritorial::factory()->create(['municipio_id' => $municipio->id]);
        $eliminada && $ubicacion->delete();

        $this->delete(route('admin.municipios.destroy', $municipio))
            ->assertRedirect()
            ->assertInertiaFlash('toast.type', 'error');

        expect(Municipio::count())->toBe(1);
    })->with([false, true]);
});

describe('departamentos', function () {
    it('lista los departamentos con su cantidad de municipios', function () {
        administradorTerritorio();
        $sacatepequez = Departamento::factory()->create(['codigo' => '03', 'nombre' => 'Sacatepéquez', 'cabecera' => 'Antigua Guatemala']);
        Municipio::factory()->count(2)->for($sacatepequez)->create();
        Departamento::factory()->create(['codigo' => '01', 'nombre' => 'Guatemala']);

        $this->get(route('admin.departamentos.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/departamentos')
                ->has('departamentos', 2)
                ->where('departamentos.0.codigo', '01')
                ->where('departamentos.0.municipios', 0)
                ->where('departamentos.1.municipios', 2)
                ->where('departamentos.1.cabecera', 'Antigua Guatemala'));
    });

    it('agrega y actualiza un departamento', function () {
        administradorTerritorio();

        $this->post(route('admin.departamentos.store'), ['codigo' => '23', 'nombre' => 'Nuevo', 'cabecera' => 'Cabecera', 'latitud' => 14.6, 'longitud' => -90.5])
            ->assertRedirect(route('admin.departamentos.index'))
            ->assertSessionHasNoErrors();

        $departamento = Departamento::where('codigo', '23')->sole();

        $this->put(route('admin.departamentos.update', $departamento), ['codigo' => '23', 'nombre' => 'Nuevo Nombre', 'cabecera' => 'Otra', 'latitud' => null, 'longitud' => null])
            ->assertSessionHasNoErrors();

        expect($departamento->fresh())->nombre->toBe('Nuevo Nombre')->cabecera->toBe('Otra')->latitud->toBeNull();
    });

    it('valida el departamento', function (array $cambios, array $errores) {
        administradorTerritorio();
        Departamento::factory()->create(['codigo' => '03', 'nombre' => 'Sacatepéquez']);

        $this->post(route('admin.departamentos.store'), ['codigo' => '04', 'nombre' => 'Chimaltenango', 'cabecera' => 'Chimaltenango', ...$cambios])
            ->assertSessionHasErrors($errores);

        expect(Departamento::count())->toBe(1);
    })->with([
        'código repetido' => [['codigo' => '03'], ['codigo' => 'Ya existe un departamento con este código.']],
        'código con formato inválido' => [['codigo' => '4'], ['codigo' => 'El código son dos dígitos, por ejemplo 01.']],
        'nombre repetido' => [['nombre' => 'Sacatepéquez'], ['nombre' => 'Ya existe un departamento con este nombre.']],
        'sin cabecera' => [['cabecera' => ''], ['cabecera' => 'Ingresa la cabecera.']],
    ]);

    it('elimina un departamento sin municipios ni EPS', function () {
        administradorTerritorio();
        $departamento = Departamento::factory()->create();

        $this->delete(route('admin.departamentos.destroy', $departamento))->assertRedirect(route('admin.departamentos.index'));

        expect(Departamento::count())->toBe(0);
    });

    it('no elimina un departamento con municipios', function () {
        administradorTerritorio();
        $municipio = Municipio::factory()->create();

        $this->delete(route('admin.departamentos.destroy', $municipio->departamento))
            ->assertInertiaFlash('toast.type', 'error');

        expect(Departamento::count())->toBe(1);
    });

    it('no elimina un departamento con EPS ubicados en él', function () {
        administradorTerritorio();
        $departamento = Departamento::factory()->create();
        Expediente::factory()->create()->ubicaciones()->create([
            'departamento_id' => $departamento->id,
            'municipio_id' => null,
        ]);

        $this->delete(route('admin.departamentos.destroy', $departamento))
            ->assertInertiaFlash('toast.type', 'error');

        expect(Departamento::count())->toBe(1);
    });
});
