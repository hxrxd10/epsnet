<?php

use App\Models\ActorParticipante;
use App\Models\Alianza;
use App\Models\BienServicio;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use App\Models\Municipio;
use App\Models\PublicacionInvestigacion;
use App\Models\SeguimientoImpacto;
use App\Models\TransferenciaConocimiento;
use App\Models\UbicacionTerritorial;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

/**
 * Cada eje: segmento de la ruta, relación del expediente, modelo, campo de texto a comprobar
 * y un cuerpo de solicitud válido.
 */
dataset('ejes', [
    'bienes y servicios' => ['bienes-servicios', 'bienesServicios', BienServicio::class, 'descripcion', fn (): array => [
        'tipo' => 'servicio',
        'descripcion' => 'Jornada de alfabetización para adultos',
        'beneficiarios' => 'Adultos de la comunidad',
        'cantidad_beneficiarios' => 40,
        'fecha' => '2026-03-10',
    ]],
    'publicaciones' => ['publicaciones', 'publicaciones', PublicacionInvestigacion::class, 'titulo', fn (): array => [
        'titulo' => 'Impacto de la lectura comunitaria',
        'tipo' => 'articulo',
        'autores' => 'Madeley Morales, Carlos Pérez',
        'medio_publicacion' => 'Revista Educativa USAC',
        'resumen' => 'Estudio sobre hábitos de lectura.',
        'enlace' => 'https://revista.example/articulo',
        'fecha_publicacion' => '2026-02-01',
    ]],
    'transferencias' => ['transferencias', 'transferencias', TransferenciaConocimiento::class, 'actividad', fn (): array => [
        'tipo_actividad' => 'taller',
        'actividad' => 'Taller de técnicas de estudio',
        'comunidad' => 'Aldea El Progreso',
        'numero_participantes' => 25,
        'fecha' => '2026-03-05',
    ]],
    'territorio' => ['territorio', 'ubicaciones', UbicacionTerritorial::class, 'municipio_id', fn (): array => [
        'municipio_id' => ($municipio = Municipio::factory()->create())->id,
        'departamento_id' => $municipio->departamento_id,
        'comunidad' => 'Aldea Sacsuy',
        'latitud' => 14.7167,
        'longitud' => -90.6,
        'referencia' => 'A 2 km del centro de la aldea',
    ]],
    'actores' => ['actores', 'actores', ActorParticipante::class, 'contraparte', fn (): array => [
        'institucion_receptora' => 'Escuela Oficial Rural Mixta Colonia Nueva Esperanza',
        'contraparte' => 'Directora María López',
        'comunidad_beneficiada' => 'Colonia Nueva Esperanza',
    ]],
    'seguimiento' => ['seguimiento', 'seguimientos', SeguimientoImpacto::class, 'indicador', fn (): array => [
        'tipo_registro' => 'avance',
        'indicador' => 'Talleres impartidos en la comunidad',
        'avance' => 'Se completaron 4 de 8 talleres.',
        'porcentaje_avance' => 50,
        'cumplimiento' => 'parcial',
        'fecha' => '2026-04-01',
    ]],
]);

function expedienteDelEstudiante(): Expediente
{
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    return Expediente::factory()->for($estudiante)->create();
}

it('muestra el paso del eje con los registros y el avance del expediente', function (string $segmento, string $relacion, string $modelo, string $campo, Closure $cuerpo) {
    $expediente = expedienteDelEstudiante();
    $modelo::factory()->count(2)->for($expediente)->create();

    $this->get(route("estudiante.expedientes.{$segmento}.index", $expediente))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component("estudiante/pasos/{$segmento}")
            ->where('expediente.id', $expediente->id)
            ->where('expediente.nombre_carrera', $expediente->nombre_carrera)
            ->has('registros', 2)
            ->has('pasos', 7)
            ->where('pasos.6.eje', 7)
            ->where('pasos.6.registros', 0)
            ->where('pasos', fn ($pasos): bool => collect($pasos)
                ->contains(fn (array $paso): bool => str_ends_with($paso['href'], "/{$segmento}") && $paso['registros'] === 2)));
})->with('ejes');

it('recuerda el eje visitado para retomar el llenado', function (string $segmento) {
    $expediente = expedienteDelEstudiante();

    $this->get(route("estudiante.expedientes.{$segmento}.index", $expediente))->assertOk();

    $this->get(route('estudiante.expedientes.show', $expediente))
        ->assertRedirect(route("estudiante.expedientes.{$segmento}.index", $expediente));
})->with(['bienes-servicios', 'publicaciones', 'transferencias', 'territorio', 'actores', 'seguimiento']);

it('guarda un registro nuevo en el expediente', function (string $segmento, string $relacion, string $modelo, string $campo, Closure $cuerpo) {
    $expediente = expedienteDelEstudiante();
    $datos = $cuerpo();

    $this->post(route("estudiante.expedientes.{$segmento}.store", $expediente), $datos)
        ->assertRedirect(route("estudiante.expedientes.{$segmento}.index", $expediente))
        ->assertSessionHasNoErrors();

    $registro = $expediente->{$relacion}()->sole();

    expect($registro->{$campo})->toBe($datos[$campo]);
})->with('ejes');

it('actualiza un registro del expediente', function (string $segmento, string $relacion, string $modelo, string $campo, Closure $cuerpo) {
    $expediente = expedienteDelEstudiante();
    $registro = $modelo::factory()->for($expediente)->create();
    $datos = $cuerpo();

    $this->put(route("estudiante.expedientes.{$segmento}.update", [$expediente, $registro->id]), $datos)
        ->assertRedirect(route("estudiante.expedientes.{$segmento}.index", $expediente))
        ->assertSessionHasNoErrors();

    expect($registro->fresh()->{$campo})->toBe($datos[$campo]);
})->with('ejes');

it('elimina un registro del expediente', function (string $segmento, string $relacion, string $modelo, string $campo, Closure $cuerpo) {
    $expediente = expedienteDelEstudiante();
    $registro = $modelo::factory()->for($expediente)->create();

    $this->delete(route("estudiante.expedientes.{$segmento}.destroy", [$expediente, $registro->id]))
        ->assertRedirect(route("estudiante.expedientes.{$segmento}.index", $expediente));

    $this->assertSoftDeleted($registro);
})->with('ejes');

it('no permite tocar registros de otro expediente', function (string $segmento, string $relacion, string $modelo, string $campo, Closure $cuerpo) {
    $expediente = expedienteDelEstudiante();
    $otroExpedienteDelMismoEstudiante = Expediente::factory()->for($expediente->estudiante)->create();
    $ajeno = $modelo::factory()->for($otroExpedienteDelMismoEstudiante)->create();
    $valorOriginal = $ajeno->{$campo};

    $this->put(route("estudiante.expedientes.{$segmento}.update", [$expediente, $ajeno->id]), $cuerpo())->assertNotFound();
    $this->delete(route("estudiante.expedientes.{$segmento}.destroy", [$expediente, $ajeno->id]))->assertNotFound();

    expect($ajeno->fresh())->not->toBeNull()
        ->and($ajeno->fresh()->{$campo})->toBe($valorOriginal);
})->with('ejes');

it('oculta por completo los expedientes de otros estudiantes', function (string $segmento, string $relacion, string $modelo, string $campo, Closure $cuerpo) {
    RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());
    $ajeno = Expediente::factory()->create();
    $registro = $modelo::factory()->for($ajeno)->create();

    $this->get(route("estudiante.expedientes.{$segmento}.index", $ajeno))->assertNotFound();
    $this->post(route("estudiante.expedientes.{$segmento}.store", $ajeno), $cuerpo())->assertNotFound();
    $this->put(route("estudiante.expedientes.{$segmento}.update", [$ajeno, $registro->id]), $cuerpo())->assertNotFound();
    $this->delete(route("estudiante.expedientes.{$segmento}.destroy", [$ajeno, $registro->id]))->assertNotFound();

    expect($ajeno->{$relacion}()->count())->toBe(1);
})->with('ejes');

it('envía a los invitados al acceso de estudiantes', function (string $segmento) {
    $expediente = Expediente::factory()->create();

    $this->get(route("estudiante.expedientes.{$segmento}.index", $expediente))
        ->assertRedirect(route('login'));
})->with(['bienes-servicios', 'publicaciones', 'transferencias', 'territorio', 'actores', 'seguimiento']);

it('exige los campos obligatorios de cada eje', function (string $segmento, array $obligatorios) {
    $expediente = expedienteDelEstudiante();

    $this->post(route("estudiante.expedientes.{$segmento}.store", $expediente), [])
        ->assertSessionHasErrors($obligatorios);

    expect($expediente->bienesServicios()->count())->toBe(0);
})->with([
    'bienes y servicios' => ['bienes-servicios', ['tipo', 'descripcion', 'fecha']],
    'publicaciones' => ['publicaciones', ['titulo', 'tipo', 'autores']],
    'transferencias' => ['transferencias', ['tipo_actividad', 'actividad', 'comunidad', 'fecha']],
    'territorio' => ['territorio', ['departamento_id', 'municipio_id']],
    'actores' => ['actores', ['institucion_receptora', 'contraparte', 'comunidad_beneficiada']],
    'seguimiento' => ['seguimiento', ['tipo_registro', 'indicador', 'fecha']],
]);

it('acepta contenidos de unas 4000 palabras y rechaza los que exceden el límite', function () {
    $expediente = expedienteDelEstudiante();
    $cuatroMilPalabras = implode(' ', array_fill(0, 4000, 'estudio'));
    $base = ['tipo' => 'bien', 'fecha' => '2026-03-10'];

    $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [...$base, 'descripcion' => $cuatroMilPalabras])
        ->assertSessionHasNoErrors();

    expect($expediente->bienesServicios()->sole()->descripcion)->toBe($cuatroMilPalabras);

    $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), [...$base, 'descripcion' => str_repeat('a', 40001)])
        ->assertSessionHasErrors(['descripcion' => 'La descripción no puede tener más de 40000 caracteres.']);
});

it('acepta un título de investigación de 4000 palabras', function () {
    $expediente = expedienteDelEstudiante();
    $titulo = implode(' ', array_fill(0, 4000, 'estudio'));

    $this->post(route('estudiante.expedientes.publicaciones.store', $expediente), [
        'titulo' => $titulo,
        'tipo' => 'tesis',
        'autores' => 'Madeley Morales',
    ])->assertSessionHasNoErrors();

    expect($expediente->publicaciones()->sole()->titulo)->toBe($titulo);
});

it('rechaza fechas futuras en los registros', function (string $segmento, string $campo, Closure $cuerpo) {
    $expediente = expedienteDelEstudiante();

    $this->post(route("estudiante.expedientes.{$segmento}.store", $expediente), [...$cuerpo(), $campo => now()->addDay()->toDateString()])
        ->assertSessionHasErrors($campo);
})->with([
    'bienes y servicios' => ['bienes-servicios', 'fecha', fn () => ['tipo' => 'bien', 'descripcion' => 'Mobiliario']],
    'publicaciones' => ['publicaciones', 'fecha_publicacion', fn () => ['titulo' => 'T', 'tipo' => 'tesis', 'autores' => 'A']],
    'transferencias' => ['transferencias', 'fecha', fn () => ['tipo_actividad' => 'taller', 'actividad' => 'A', 'comunidad' => 'C']],
    'seguimiento' => ['seguimiento', 'fecha', fn () => ['tipo_registro' => 'avance', 'indicador' => 'I']],
]);

it('entrega al paso de territorio la configuración de Google Maps', function () {
    config()->set('services.google_maps', ['key' => 'clave-de-prueba', 'map_id' => 'MAPA_PRUEBA']);
    $expediente = expedienteDelEstudiante();

    $this->get(route('estudiante.expedientes.territorio.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page
            ->where('googleMaps.key', 'clave-de-prueba')
            ->where('googleMaps.mapId', 'MAPA_PRUEBA'));
});

it('ofrece los departamentos y los municipios del catálogo para ubicar el EPS', function () {
    $sacatepequez = Departamento::factory()->create();
    Municipio::factory()->for($sacatepequez)->create(['nombre' => 'Antigua Guatemala']);
    Municipio::factory()->for($sacatepequez)->create(['nombre' => 'Jocotenango']);
    Municipio::factory()->create(['nombre' => 'Flores']);
    Departamento::factory()->count(2)->create();
    $expediente = expedienteDelEstudiante();

    $this->get(route('estudiante.expedientes.territorio.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page
            ->has('departamentos', 4)
            ->has('municipios', 3)
            ->where('municipios.0.label', 'Antigua Guatemala')
            ->where('municipios.0.departamento_id', (string) $sacatepequez->id));
});

it('valida la ubicación dentro del territorio de Guatemala', function (array $cambios, array $errores) {
    $expediente = expedienteDelEstudiante();
    $municipio = Municipio::factory()->create();

    $this->post(route('estudiante.expedientes.territorio.store', $expediente), [
        'departamento_id' => $municipio->departamento_id,
        'municipio_id' => $municipio->id,
        ...$cambios,
    ])->assertSessionHasErrors($errores);

    expect($expediente->ubicaciones()->count())->toBe(0);
})->with([
    'latitud sin longitud' => [['latitud' => 14.56], ['longitud' => 'Ingresa la longitud junto con la latitud.']],
    'longitud sin latitud' => [['longitud' => -90.73], ['latitud' => 'Ingresa la latitud junto con la longitud.']],
    'latitud fuera del país' => [['latitud' => 40.7, 'longitud' => -90.7], ['latitud' => 'La latitud debe estar dentro del territorio de Guatemala (entre 13.5 y 18.5).']],
    'longitud fuera del país' => [['latitud' => 14.5, 'longitud' => -74.0], ['longitud' => 'La longitud debe estar dentro del territorio de Guatemala (entre -92.5 y -88).']],
    'departamento inexistente' => [['departamento_id' => 9999], ['departamento_id' => 'La opción elegida en el departamento no es válida.']],
    'municipio inexistente' => [['municipio_id' => 9999], ['municipio_id' => 'El municipio no pertenece al departamento elegido.']],
    'municipio sin elegir' => [['municipio_id' => ''], ['municipio_id' => 'Ingresa el municipio.']],
]);

it('no acepta un municipio que pertenece a otro departamento', function () {
    $expediente = expedienteDelEstudiante();
    $municipio = Municipio::factory()->create();

    $this->post(route('estudiante.expedientes.territorio.store', $expediente), [
        'departamento_id' => Departamento::factory()->create()->id,
        'municipio_id' => $municipio->id,
    ])->assertSessionHasErrors(['municipio_id' => 'El municipio no pertenece al departamento elegido.']);

    expect($expediente->ubicaciones()->count())->toBe(0);
});

it('guarda la ubicación con la referencia territorial cuando no hay coordenadas exactas', function () {
    $expediente = expedienteDelEstudiante();
    $municipio = Municipio::factory()->create();

    $this->post(route('estudiante.expedientes.territorio.store', $expediente), [
        'departamento_id' => $municipio->departamento_id,
        'municipio_id' => $municipio->id,
        'referencia' => 'Cerca del parque central',
    ])->assertSessionHasNoErrors();

    expect($expediente->ubicaciones()->sole())
        ->departamento_id->toBe($municipio->departamento_id)
        ->municipio_id->toBe($municipio->id)
        ->latitud->toBeNull()
        ->referencia->toBe('Cerca del parque central');
});

describe('institución receptora (la escribe el estudiante)', function () {
    it('acepta cualquier nombre sin tocar el catálogo de instituciones aliadas', function () {
        $expediente = expedienteDelEstudiante();

        $this->post(route('estudiante.expedientes.actores.store', $expediente), [
            'institucion_receptora' => 'Escuela Oficial Rural Mixta El Progreso',
            'contraparte' => 'Directora María López',
            'comunidad_beneficiada' => 'Aldea El Progreso',
        ])->assertSessionHasNoErrors();

        expect($expediente->actores()->sole()->institucion_receptora)->toBe('Escuela Oficial Rural Mixta El Progreso')
            ->and(InstitucionAliada::count())->toBe(0);
    });

    it('permite que dos estudiantes escriban la misma institución', function () {
        $expediente = expedienteDelEstudiante();
        Expediente::factory()->create()->actores()->create(['institucion_receptora' => 'Centro de Salud Zona 5', 'contraparte' => 'Dr. Ramírez', 'comunidad_beneficiada' => 'Zona 5']);

        $this->post(route('estudiante.expedientes.actores.store', $expediente), [
            'institucion_receptora' => 'Centro de Salud Zona 5',
            'contraparte' => 'Enfermera Ortiz',
            'comunidad_beneficiada' => 'Zona 5',
        ])->assertSessionHasNoErrors();

        expect($expediente->actores()->count())->toBe(1);
    });
});

describe('instituciones aliadas (del catálogo de DIGEU)', function () {
    it('ofrece el catálogo ordenado por nombre, con su tipo, junto con las aliadas del EPS', function () {
        $expediente = expedienteDelEstudiante();
        $norte = InstitucionAliada::factory()->create(['nombre' => 'Ministerio de Salud', 'tipo' => 'Gobierno central']);
        InstitucionAliada::factory()->create(['nombre' => 'Cruz Roja', 'tipo' => null]);
        $expediente->alianzas()->create(['institucion_aliada_id' => $norte->id, 'aporte' => 'Vacunas']);

        $this->get(route('estudiante.expedientes.actores.index', $expediente))
            ->assertInertia(fn (Assert $page) => $page
                ->where('instituciones.0.label', 'Cruz Roja')
                ->where('instituciones.1.label', 'Ministerio de Salud (Gobierno central)')
                ->where('alianzas.0.institucion', 'Ministerio de Salud')
                ->where('alianzas.0.aporte', 'Vacunas'));
    });

    it('registra, actualiza y elimina una alianza con una institución del catálogo', function () {
        $expediente = expedienteDelEstudiante();
        $ministerio = InstitucionAliada::factory()->create();
        $ong = InstitucionAliada::factory()->create();

        $this->post(route('estudiante.expedientes.alianzas.store', $expediente), ['institucion_aliada_id' => $ministerio->id, 'aporte' => 'Materiales'])
            ->assertRedirect(route('estudiante.expedientes.actores.index', $expediente))
            ->assertSessionHasNoErrors();

        $alianza = $expediente->alianzas()->sole();

        $this->put(route('estudiante.expedientes.alianzas.update', [$expediente, $alianza->id]), ['institucion_aliada_id' => $ong->id, 'aporte' => 'Capacitación'])
            ->assertSessionHasNoErrors();
        expect($alianza->fresh())->institucion_aliada_id->toBe($ong->id)->aporte->toBe('Capacitación');

        $this->delete(route('estudiante.expedientes.alianzas.destroy', [$expediente, $alianza->id]));
        $this->assertSoftDeleted($alianza);
    });

    it('permite dejar el aporte sin describir', function () {
        $expediente = expedienteDelEstudiante();

        $this->post(route('estudiante.expedientes.alianzas.store', $expediente), ['institucion_aliada_id' => InstitucionAliada::factory()->create()->id])
            ->assertSessionHasNoErrors();

        expect($expediente->alianzas()->sole()->aporte)->toBeNull();
    });

    it('exige elegir una institución del catálogo y no deja al estudiante crearla', function () {
        $expediente = expedienteDelEstudiante();

        $this->post(route('estudiante.expedientes.alianzas.store', $expediente), ['institucion_nombre' => 'Fundación Nueva'])
            ->assertSessionHasErrors(['institucion_aliada_id' => 'Selecciona la institución aliada. Si no aparece en la lista, pide a DIGEU que la agregue.']);

        expect(InstitucionAliada::count())->toBe(0)->and($expediente->alianzas()->count())->toBe(0);
    });

    it('no acepta una institución inexistente o eliminada', function (string $caso) {
        $expediente = expedienteDelEstudiante();
        $eliminada = InstitucionAliada::factory()->create();
        $eliminada->delete();

        $this->post(route('estudiante.expedientes.alianzas.store', $expediente), ['institucion_aliada_id' => $caso === 'inexistente' ? 9999 : $eliminada->id])
            ->assertSessionHasErrors(['institucion_aliada_id' => 'La institución elegida ya no está disponible. Selecciona otra de la lista.']);
    })->with(['inexistente', 'eliminada']);

    it('no permite repetir la misma institución en un EPS, pero sí en otro', function () {
        $expediente = expedienteDelEstudiante();
        $ministerio = InstitucionAliada::factory()->create();
        Alianza::factory()->create(['expediente_id' => Expediente::factory()->create()->id, 'institucion_aliada_id' => $ministerio->id]);

        $this->post(route('estudiante.expedientes.alianzas.store', $expediente), ['institucion_aliada_id' => $ministerio->id])->assertSessionHasNoErrors();
        $this->post(route('estudiante.expedientes.alianzas.store', $expediente), ['institucion_aliada_id' => $ministerio->id])
            ->assertSessionHasErrors(['institucion_aliada_id' => 'Esta institución ya está registrada como aliada de tu EPS.']);

        expect($expediente->alianzas()->count())->toBe(1);
    });

    it('permite volver a elegir la misma institución al editar su propia alianza y tras eliminarla', function () {
        $expediente = expedienteDelEstudiante();
        $ministerio = InstitucionAliada::factory()->create();
        $alianza = $expediente->alianzas()->create(['institucion_aliada_id' => $ministerio->id]);

        $this->put(route('estudiante.expedientes.alianzas.update', [$expediente, $alianza->id]), ['institucion_aliada_id' => $ministerio->id, 'aporte' => 'Nuevo'])
            ->assertSessionHasNoErrors();

        $alianza->delete();

        $this->post(route('estudiante.expedientes.alianzas.store', $expediente), ['institucion_aliada_id' => $ministerio->id])->assertSessionHasNoErrors();
    });

    it('no permite tocar las alianzas del EPS de otro estudiante', function () {
        expedienteDelEstudiante();
        $ajeno = Expediente::factory()->create();
        $alianza = Alianza::factory()->create(['expediente_id' => $ajeno->id]);

        $this->post(route('estudiante.expedientes.alianzas.store', $ajeno), ['institucion_aliada_id' => InstitucionAliada::factory()->create()->id])->assertNotFound();
        $this->delete(route('estudiante.expedientes.alianzas.destroy', [$ajeno, $alianza->id]))->assertNotFound();

        expect($ajeno->alianzas()->count())->toBe(1);
    });
});

it('exige la evaluación de impacto solo en el registro final', function () {
    $expediente = expedienteDelEstudiante();
    $base = ['indicador' => 'Talleres impartidos', 'fecha' => '2026-04-01'];

    $this->post(route('estudiante.expedientes.seguimiento.store', $expediente), [...$base, 'tipo_registro' => 'avance'])
        ->assertSessionHasNoErrors();

    $this->post(route('estudiante.expedientes.seguimiento.store', $expediente), [...$base, 'tipo_registro' => 'evaluacion_final'])
        ->assertSessionHasErrors(['evaluacion_impacto' => 'Ingresa la evaluación de impacto.']);

    $this->post(route('estudiante.expedientes.seguimiento.store', $expediente), [
        ...$base,
        'tipo_registro' => 'evaluacion_final',
        'evaluacion_impacto' => 'El programa mejoró la lectura de 40 niños.',
    ])->assertSessionHasNoErrors();

    expect($expediente->seguimientos()->count())->toBe(2);
});

it('permite repetir el seguimiento en distintos momentos del EPS', function () {
    $expediente = expedienteDelEstudiante();

    foreach (['2026-03-01', '2026-05-01', '2026-07-01'] as $fecha) {
        $this->post(route('estudiante.expedientes.seguimiento.store', $expediente), [
            'tipo_registro' => 'avance',
            'indicador' => 'Talleres impartidos',
            'porcentaje_avance' => 30,
            'fecha' => $fecha,
        ])->assertSessionHasNoErrors();
    }

    expect($expediente->seguimientos()->count())->toBe(3);
});

it('rechaza un porcentaje de avance fuera de rango', function (int $porcentaje) {
    $expediente = expedienteDelEstudiante();

    $this->post(route('estudiante.expedientes.seguimiento.store', $expediente), [
        'tipo_registro' => 'avance',
        'indicador' => 'Talleres',
        'porcentaje_avance' => $porcentaje,
        'fecha' => '2026-04-01',
    ])->assertSessionHasErrors('porcentaje_avance');
})->with([-1, 101]);
