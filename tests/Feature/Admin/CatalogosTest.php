<?php

use App\Enums\TipoCatalogo;
use App\Models\BienServicio;
use App\Models\Catalogo;
use App\Models\Expediente;
use App\Models\TransferenciaConocimiento;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function administrador(): User
{
    $usuario = User::factory()->administrador()->create();
    test()->actingAs($usuario);

    return $usuario;
}

describe('acceso', function () {
    it('envía a los invitados a iniciar sesión', function () {
        $this->get(route('admin.datos'))->assertRedirect(route('login'));
    });

    it('niega el acceso a quien no es administrador', function (string $estado) {
        $this->actingAs(User::factory()->{$estado}()->create());

        $this->get(route('admin.datos'))->assertForbidden();
        $this->get(route('admin.catalogos.index', 'bienes-servicios'))->assertForbidden();
        $this->post(route('admin.catalogos.store', 'bienes-servicios'), ['nombre' => 'X', 'categoria' => 'bien', 'activo' => true])->assertForbidden();

        expect(Catalogo::count())->toBe(0);
    })->with(['invitado', 'estudiante']);

    it('niega el acceso a un usuario sin rol', function () {
        $this->actingAs(User::factory()->create())->get(route('admin.datos'))->assertForbidden();
    });
});

describe('manejo de datos', function () {
    it('lista los catálogos con su cantidad de elementos', function () {
        administrador();
        Catalogo::factory()->count(2)->create();
        Catalogo::factory()->inactivo()->create();
        Catalogo::factory()->deAcciones()->create();

        $this->get(route('admin.datos'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/datos')
                ->has('catalogos', 2)
                ->where('catalogos.0.clave', 'bienes-servicios')
                ->where('catalogos.0.total', 3)
                ->where('catalogos.0.activos', 2)
                ->where('catalogos.1.clave', 'acciones')
                ->where('catalogos.1.total', 1));
    });

    it('muestra solo los elementos del catálogo elegido, con su uso', function () {
        administrador();
        $enUso = Catalogo::factory()->create(['nombre' => 'Biblioteca escolar', 'categoria' => 'bien']);
        Catalogo::factory()->create(['nombre' => 'Atención médica']);
        Catalogo::factory()->deAcciones()->create(['nombre' => 'Taller de lectura']);
        BienServicio::factory()->for(Expediente::factory())->create(['catalogo_id' => $enUso->id]);

        $this->get(route('admin.catalogos.index', 'bienes-servicios'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/catalogo')
                ->where('catalogo.clave', 'bienes-servicios')
                ->where('catalogo.usa_categoria', true)
                ->has('elementos', 2)
                ->where('elementos.0.nombre', 'Atención médica')
                ->where('elementos.0.en_uso', false)
                ->where('elementos.1.nombre', 'Biblioteca escolar')
                ->where('elementos.1.categoria_etiqueta', 'Bien')
                ->where('elementos.1.en_uso', true));
    });

    it('responde 404 ante un catálogo que no existe', function () {
        administrador();

        $this->get('/admin/catalogos/inexistente')->assertNotFound();
    });
});

describe('creación y edición', function () {
    it('agrega un elemento al catálogo', function () {
        administrador();

        $this->post(route('admin.catalogos.store', 'bienes-servicios'), [
            'nombre' => 'Biblioteca escolar',
            'descripcion' => 'Dotación de libros',
            'categoria' => 'bien',
            'activo' => true,
        ])->assertRedirect(route('admin.catalogos.index', 'bienes-servicios'))->assertSessionHasNoErrors();

        expect(Catalogo::sole())
            ->catalogo->toBe(TipoCatalogo::BienesServicios)
            ->nombre->toBe('Biblioteca escolar')
            ->categoria->toBe('bien')
            ->activo->toBeTrue();
    });

    it('agrega acciones sin categoría', function () {
        administrador();

        $this->post(route('admin.catalogos.store', 'acciones'), ['nombre' => 'Taller de lectura', 'activo' => true])
            ->assertSessionHasNoErrors();

        expect(Catalogo::sole())->catalogo->toBe(TipoCatalogo::Acciones)->categoria->toBeNull();
    });

    it('valida los datos del elemento', function (array $datos, array $errores) {
        administrador();
        Catalogo::factory()->create(['nombre' => 'Existente']);

        $this->post(route('admin.catalogos.store', 'bienes-servicios'), array_merge(['nombre' => 'Nuevo', 'categoria' => 'bien', 'activo' => true], $datos))
            ->assertSessionHasErrors($errores);

        expect(Catalogo::count())->toBe(1);
    })->with([
        'sin nombre' => [['nombre' => ''], ['nombre' => 'Ingresa el nombre.']],
        'nombre repetido en el catálogo' => [['nombre' => 'Existente'], ['nombre' => 'Ya existe un elemento con este nombre en el catálogo.']],
        'sin categoría' => [['categoria' => ''], ['categoria' => 'Selecciona la categoría.']],
        'categoría inválida' => [['categoria' => 'otra'], ['categoria']],
        'sin estado' => [['activo' => null], ['activo']],
    ]);

    it('permite el mismo nombre en catálogos distintos', function () {
        administrador();
        Catalogo::factory()->create(['nombre' => 'Capacitación']);

        $this->post(route('admin.catalogos.store', 'acciones'), ['nombre' => 'Capacitación', 'activo' => true])
            ->assertSessionHasNoErrors();

        expect(Catalogo::count())->toBe(2);
    });

    it('actualiza un elemento y permite conservar su propio nombre', function () {
        administrador();
        $elemento = Catalogo::factory()->create(['nombre' => 'Original']);

        $this->put(route('admin.catalogos.update', ['bienes-servicios', $elemento->id]), [
            'nombre' => 'Original',
            'descripcion' => 'Nueva descripción',
            'categoria' => 'bien',
            'activo' => false,
        ])->assertRedirect(route('admin.catalogos.index', 'bienes-servicios'))->assertSessionHasNoErrors();

        expect($elemento->fresh())
            ->descripcion->toBe('Nueva descripción')
            ->categoria->toBe('bien')
            ->activo->toBeFalse();
    });

    it('no edita elementos de otro catálogo', function () {
        administrador();
        $accion = Catalogo::factory()->deAcciones()->create();

        $this->put(route('admin.catalogos.update', ['bienes-servicios', $accion->id]), ['nombre' => 'X', 'categoria' => 'bien', 'activo' => true])
            ->assertNotFound();
    });
});

describe('eliminación', function () {
    it('elimina un elemento que nadie usa', function () {
        administrador();
        $elemento = Catalogo::factory()->create();

        $this->delete(route('admin.catalogos.destroy', ['bienes-servicios', $elemento->id]))
            ->assertRedirect(route('admin.catalogos.index', 'bienes-servicios'));

        expect(Catalogo::count())->toBe(0);
    });

    it('no elimina un elemento que ya usan los estudiantes', function (Closure $uso) {
        administrador();
        $elemento = Catalogo::factory()->create();
        $uso($elemento);

        $this->delete(route('admin.catalogos.destroy', ['bienes-servicios', $elemento->id]))
            ->assertRedirect(route('admin.catalogos.index', 'bienes-servicios'));

        expect($elemento->fresh())->not->toBeNull()
            ->and($elemento->estaEnUso())->toBeTrue();
    })->with([
        'en bienes y servicios' => [fn (Catalogo $elemento) => BienServicio::factory()->for(Expediente::factory())->create(['catalogo_id' => $elemento->id])],
        'en un registro eliminado' => [fn (Catalogo $elemento) => BienServicio::factory()->for(Expediente::factory())->create(['catalogo_id' => $elemento->id])->delete()],
        'en transferencias' => [fn (Catalogo $elemento) => TransferenciaConocimiento::factory()->for(Expediente::factory())->create(['catalogo_id' => $elemento->id])],
    ]);
});
