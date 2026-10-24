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

/**
 * Línea, política, área y ODS válidos para un bien o servicio nuevo.
 *
 * @return array<string, int>
 */
function vinculosDeBien(): array
{
    return collect(TipoCatalogo::BienesServicios->vinculos())
        ->map(fn (TipoCatalogo $origen): int => Catalogo::factory()->del($origen)->create()->id)
        ->all();
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
                ->has('catalogos', count(TipoCatalogo::cases()))
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
            ...vinculosDeBien(),
        ])->assertRedirect(route('admin.catalogos.index', 'bienes-servicios'))->assertSessionHasNoErrors();

        expect(Catalogo::delCatalogo(TipoCatalogo::BienesServicios)->sole())
            ->catalogo->toBe(TipoCatalogo::BienesServicios)
            ->nombre->toBe('Biblioteca escolar')
            ->categoria->toBe('bien')
            ->activo->toBeTrue();
    });

    it('guarda el valor base en quetzales de un bien o servicio', function () {
        administrador();

        $this->post(route('admin.catalogos.store', 'bienes-servicios'), ['nombre' => 'Pupitre', 'categoria' => 'bien', 'valor_base' => '350.50', 'activo' => true, ...vinculosDeBien()])
            ->assertSessionHasNoErrors();

        expect(Catalogo::where('nombre', 'Pupitre')->sole()->valor_base)->toBe('350.50');

        $this->get(route('admin.catalogos.index', 'bienes-servicios'))
            ->assertInertia(fn (Assert $page) => $page->where('catalogo.usa_valor_base', true)->where('elementos.0.valor_base', '350.50'));
    });

    it('valida el valor base y no lo acepta en catálogos que no lo usan', function (string $catalogo, array $datos, string $campo) {
        administrador();

        $this->post(route('admin.catalogos.store', $catalogo), ['nombre' => 'Nuevo', 'categoria' => 'bien', 'activo' => true, ...$datos])
            ->assertSessionHasErrors($campo);

        expect(Catalogo::count())->toBe(0);
    })->with([
        'negativo' => ['bienes-servicios', ['valor_base' => '-1'], 'valor_base'],
        'tres decimales' => ['bienes-servicios', ['valor_base' => '10.555'], 'valor_base'],
        'no es número' => ['bienes-servicios', ['valor_base' => 'mucho'], 'valor_base'],
        'en acciones' => ['acciones', ['valor_base' => '100'], 'valor_base'],
    ]);

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
            ...vinculosDeBien(),
        ])->assertRedirect(route('admin.catalogos.index', 'bienes-servicios'))->assertSessionHasNoErrors();

        expect($elemento->fresh())
            ->descripcion->toBe('Nueva descripción')
            ->categoria->toBe('bien')
            ->activo->toBeFalse();
    });

    it('no edita elementos de otro catálogo', function () {
        administrador();
        $accion = Catalogo::factory()->deAcciones()->create();

        $this->put(route('admin.catalogos.update', ['bienes-servicios', $accion->id]), ['nombre' => 'X', 'categoria' => 'bien', 'activo' => true, ...vinculosDeBien()])
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

describe('vínculos de los bienes y servicios', function () {
    it('mantiene las líneas, políticas, áreas y ODS como catálogos propios', function (string $clave) {
        administrador();

        $this->post(route('admin.catalogos.store', $clave), ['nombre' => 'Agua y saneamiento', 'activo' => true])
            ->assertRedirect(route('admin.catalogos.index', $clave))
            ->assertSessionHasNoErrors();

        expect(Catalogo::sole())->catalogo->value->toBe($clave)->categoria->toBeNull();

        $this->get(route('admin.catalogos.index', $clave))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('catalogo.clave', $clave)->where('catalogo.vinculos', [])->has('elementos', 1));
    })->with(['lineas-intervencion', 'politicas-publicas', 'areas-intervencion', 'ods']);

    it('guarda el bien o servicio con su línea, política, área y ODS y los muestra en el listado', function () {
        administrador();
        $vinculos = vinculosDeBien();

        $this->post(route('admin.catalogos.store', 'bienes-servicios'), ['nombre' => 'Huerto escolar', 'categoria' => 'bien', 'activo' => true, ...$vinculos])
            ->assertSessionHasNoErrors();

        $bien = Catalogo::where('nombre', 'Huerto escolar')->sole();
        expect($bien->only(array_keys($vinculos)))->toBe($vinculos);

        $this->get(route('admin.catalogos.index', 'bienes-servicios'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('catalogo.vinculos', 4)
                ->where('catalogo.vinculos.0.campo', 'linea_intervencion_id')
                ->where('catalogo.vinculos.0.opciones.0.value', (string) $vinculos['linea_intervencion_id'])
                ->where('elementos.0.vinculos.ods_id.id', $vinculos['ods_id'])
                ->where('elementos.0.vinculos.ods_id.nombre', Catalogo::find($vinculos['ods_id'])->nombre));
    });

    it('exige los cuatro vínculos y que cada uno sea de su catálogo', function () {
        administrador();
        $vinculos = vinculosDeBien();
        $area = $vinculos['area_intervencion_id'];

        $this->post(route('admin.catalogos.store', 'bienes-servicios'), ['nombre' => 'Huerto', 'categoria' => 'bien', 'activo' => true])
            ->assertSessionHasErrors(['linea_intervencion_id', 'politica_publica_id', 'area_intervencion_id', 'ods_id']);

        $this->post(route('admin.catalogos.store', 'bienes-servicios'), ['nombre' => 'Huerto', 'categoria' => 'bien', 'activo' => true, ...$vinculos, 'linea_intervencion_id' => $area])
            ->assertSessionHasErrors(['linea_intervencion_id']);

        expect(Catalogo::where('nombre', 'Huerto')->exists())->toBeFalse();
    });

    it('no acepta vínculos en los demás catálogos', function () {
        administrador();
        $linea = Catalogo::factory()->del(TipoCatalogo::LineasIntervencion)->create();

        $this->post(route('admin.catalogos.store', 'acciones'), ['nombre' => 'Taller', 'activo' => true, 'linea_intervencion_id' => $linea->id])
            ->assertSessionHasErrors('linea_intervencion_id');
    });

    it('cuenta los bienes anclados a una línea y no la elimina mientras los tenga', function () {
        administrador();
        $bien = Catalogo::factory()->anclado()->create();
        $linea = $bien->lineaIntervencion;

        $this->get(route('admin.catalogos.index', 'lineas-intervencion'))
            ->assertInertia(fn (Assert $page) => $page->where('elementos.0.id', $linea->id)->where('elementos.0.anclados', 1)->where('elementos.0.en_uso', true));

        $this->delete(route('admin.catalogos.destroy', ['lineas-intervencion', $linea->id]))
            ->assertInertiaFlash('toast.type', 'error');

        expect($linea->fresh())->not->toBeNull();
    });

    it('ordena los ODS por su número', function () {
        administrador();
        foreach (['ODS 13: Acción por el clima', 'ODS 3: Salud y bienestar', 'ODS 6: Agua limpia y saneamiento'] as $nombre) {
            Catalogo::factory()->del(TipoCatalogo::Ods)->create(['nombre' => $nombre]);
        }

        $this->get(route('admin.catalogos.index', 'ods'))
            ->assertInertia(fn (Assert $page) => $page->where('elementos', fn ($elementos) => collect($elementos)->pluck('nombre')->all() === [
                'ODS 3: Salud y bienestar', 'ODS 6: Agua limpia y saneamiento', 'ODS 13: Acción por el clima',
            ]));
    });

    it('acepta nombres largos como los de las políticas públicas', function () {
        administrador();
        $nombre = str_repeat('Política Nacional del cambio Climático. ', 10);

        $this->post(route('admin.catalogos.store', 'politicas-publicas'), ['nombre' => trim($nombre), 'activo' => true])->assertSessionHasNoErrors();

        expect(Catalogo::sole()->nombre)->toBe(trim($nombre));
    });
});

it('no elimina una población vulnerable que ya marcó algún bien o servicio', function () {
    administrador();
    $mujeres = Catalogo::factory()->del(TipoCatalogo::PoblacionesVulnerables)->create(['nombre' => 'Mujeres']);
    BienServicio::factory()->create()->poblaciones()->attach($mujeres);

    $this->delete(route('admin.catalogos.destroy', ['poblaciones-vulnerables', $mujeres->id]))->assertInertiaFlash('toast.type', 'error');

    expect($mujeres->fresh())->not->toBeNull();
});
