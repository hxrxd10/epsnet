<?php

use App\Models\Catalogo;
use App\Models\Estudiante;
use App\Models\Expediente;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

function expedienteParaCatalogos(): Expediente
{
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    return Expediente::factory()->for($estudiante)->create();
}

describe('bienes y servicios', function () {
    it('ofrece los elementos activos del catálogo y los que el expediente ya usa', function () {
        $expediente = expedienteParaCatalogos();
        Catalogo::factory()->create(['nombre' => 'Activo']);
        Catalogo::factory()->inactivo()->create(['nombre' => 'Inactivo']);
        $usadoInactivo = Catalogo::factory()->inactivo()->create(['nombre' => 'Usado e inactivo']);
        Catalogo::factory()->deAcciones()->create(['nombre' => 'De otro catálogo']);
        $expediente->bienesServicios()->create(['catalogo_id' => $usadoInactivo->id, 'tipo' => 'servicio', 'descripcion' => 'X', 'fecha' => '2026-03-01']);

        $this->get(route('estudiante.expedientes.bienes-servicios.index', $expediente))
            ->assertInertia(fn (Assert $page) => $page
                ->has('catalogo', 2)
                ->where('catalogo.0.label', 'Activo')
                ->where('catalogo.1.label', 'Usado e inactivo')
                ->where('registros.0.catalogo_id', $usadoInactivo->id));
    });

    it('toma el tipo de la categoría del elemento elegido', function () {
        $expediente = expedienteParaCatalogos();
        $elemento = Catalogo::factory()->create(['categoria' => 'bien']);

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [
            'catalogo_id' => $elemento->id,
            'tipo' => 'servicio',
            'descripcion' => 'Se entregaron 200 libros',
            'fecha' => '2026-03-10',
        ])->assertSessionHasNoErrors();

        expect($expediente->bienesServicios()->sole())
            ->catalogo_id->toBe($elemento->id)
            ->tipo->value->toBe('bien');
    });

    it('permite elegir un elemento sin indicar el tipo', function () {
        $expediente = expedienteParaCatalogos();
        $elemento = Catalogo::factory()->create(['categoria' => 'servicio']);

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [
            'catalogo_id' => $elemento->id,
            'descripcion' => 'Jornada médica',
            'fecha' => '2026-03-10',
        ])->assertSessionHasNoErrors();

        expect($expediente->bienesServicios()->sole()->tipo->value)->toBe('servicio');
    });

    it('exige el tipo cuando no se elige un elemento del catálogo', function () {
        $expediente = expedienteParaCatalogos();

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [
            'descripcion' => 'Algo que no está en el catálogo',
            'fecha' => '2026-03-10',
        ])->assertSessionHasErrors('tipo');
    });

    it('exige el tipo si el elemento del catálogo no tiene categoría', function () {
        $expediente = expedienteParaCatalogos();
        $elemento = Catalogo::factory()->create(['categoria' => null]);
        $datos = ['catalogo_id' => $elemento->id, 'descripcion' => 'X', 'fecha' => '2026-03-10'];

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), $datos)
            ->assertSessionHasErrors(['tipo' => 'Selecciona si es un bien o un servicio.']);

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [...$datos, 'tipo' => 'bien'])
            ->assertSessionHasNoErrors();
    });

    it('rechaza elementos de otro catálogo o inexistentes', function (?Closure $catalogo) {
        $expediente = expedienteParaCatalogos();

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [
            'catalogo_id' => $catalogo ? $catalogo()->id : 9999,
            'descripcion' => 'X',
            'fecha' => '2026-03-10',
        ])->assertSessionHasErrors('catalogo_id');
    })->with([
        'de acciones' => [fn () => Catalogo::factory()->deAcciones()->create()],
        'inexistente' => [null],
    ]);
});

describe('transferencia de conocimiento', function () {
    it('ofrece las acciones activas del catálogo', function () {
        $expediente = expedienteParaCatalogos();
        Catalogo::factory()->deAcciones()->create(['nombre' => 'Taller de lectura']);
        Catalogo::factory()->deAcciones()->inactivo()->create();
        Catalogo::factory()->create(['nombre' => 'De bienes y servicios']);

        $this->get(route('estudiante.expedientes.transferencias.index', $expediente))
            ->assertInertia(fn (Assert $page) => $page
                ->has('catalogo', 1)
                ->where('catalogo.0.label', 'Taller de lectura'));
    });

    it('guarda la acción elegida junto con el tipo de actividad', function () {
        $expediente = expedienteParaCatalogos();
        $accion = Catalogo::factory()->deAcciones()->create();

        $this->post(route('estudiante.expedientes.transferencias.store', $expediente), [
            'catalogo_id' => $accion->id,
            'tipo_actividad' => 'taller',
            'actividad' => 'Taller para docentes',
            'comunidad' => 'Aldea El Progreso',
            'fecha' => '2026-03-05',
        ])->assertSessionHasNoErrors();

        expect($expediente->transferencias()->sole()->catalogo_id)->toBe($accion->id);
    });

    it('rechaza acciones que no son del catálogo de acciones', function () {
        $expediente = expedienteParaCatalogos();
        $bien = Catalogo::factory()->create();

        $this->post(route('estudiante.expedientes.transferencias.store', $expediente), [
            'catalogo_id' => $bien->id,
            'tipo_actividad' => 'taller',
            'actividad' => 'X',
            'comunidad' => 'Y',
            'fecha' => '2026-03-05',
        ])->assertSessionHasErrors('catalogo_id');
    });
});
