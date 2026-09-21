<?php

use App\Models\ActorParticipante;
use App\Models\BienServicio;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\InstitucionReceptora;
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
    'territorio' => ['territorio', 'ubicaciones', UbicacionTerritorial::class, 'municipio', fn (): array => [
        'departamento_id' => Departamento::factory()->create()->id,
        'municipio' => 'San Juan Sacatepéquez',
        'comunidad' => 'Aldea Sacsuy',
        'latitud' => 14.7167,
        'longitud' => -90.6,
        'referencia' => 'A 2 km del centro de la aldea',
    ]],
    'actores' => ['actores', 'actores', ActorParticipante::class, 'contraparte', fn (): array => [
        'institucion_receptora_id' => InstitucionReceptora::factory()->create()->id,
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
    'territorio' => ['territorio', ['departamento_id', 'municipio']],
    'actores' => ['actores', ['institucion_nombre', 'contraparte', 'comunidad_beneficiada']],
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

it('ofrece los departamentos como opciones para ubicar el EPS', function () {
    Departamento::factory()->count(3)->create();
    $expediente = expedienteDelEstudiante();

    $this->get(route('estudiante.expedientes.territorio.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page->has('departamentos', 3));
});

it('valida la ubicación dentro del territorio de Guatemala', function (array $cambios, array $errores) {
    $expediente = expedienteDelEstudiante();
    $departamento = Departamento::factory()->create();

    $this->post(route('estudiante.expedientes.territorio.store', $expediente), [
        'departamento_id' => $departamento->id,
        'municipio' => 'Antigua Guatemala',
        ...$cambios,
    ])->assertSessionHasErrors($errores);

    expect($expediente->ubicaciones()->count())->toBe(0);
})->with([
    'latitud sin longitud' => [['latitud' => 14.56], ['longitud' => 'Ingresa la longitud junto con la latitud.']],
    'longitud sin latitud' => [['longitud' => -90.73], ['latitud' => 'Ingresa la latitud junto con la longitud.']],
    'latitud fuera del país' => [['latitud' => 40.7, 'longitud' => -90.7], ['latitud' => 'La latitud debe estar dentro del territorio de Guatemala (entre 13.5 y 18.5).']],
    'longitud fuera del país' => [['latitud' => 14.5, 'longitud' => -74.0], ['longitud' => 'La longitud debe estar dentro del territorio de Guatemala (entre -92.5 y -88).']],
    'departamento inexistente' => [['departamento_id' => 9999], ['departamento_id' => 'La opción elegida en el departamento no es válida.']],
]);

it('guarda la ubicación con la referencia territorial cuando no hay coordenadas exactas', function () {
    $expediente = expedienteDelEstudiante();
    $departamento = Departamento::factory()->create();

    $this->post(route('estudiante.expedientes.territorio.store', $expediente), [
        'departamento_id' => $departamento->id,
        'municipio' => 'Antigua Guatemala',
        'referencia' => 'Cerca del parque central',
    ])->assertSessionHasNoErrors();

    expect($expediente->ubicaciones()->sole())
        ->departamento_id->toBe($departamento->id)
        ->latitud->toBeNull()
        ->referencia->toBe('Cerca del parque central');
});

it('registra una institución receptora nueva junto con los actores', function () {
    $expediente = expedienteDelEstudiante();

    $this->post(route('estudiante.expedientes.actores.store', $expediente), [
        'institucion_nombre' => 'Escuela Oficial Rural Mixta El Progreso',
        'institucion_tipo' => 'Educación',
        'institucion_correo_contacto' => 'director@escuela.example',
        'contraparte' => 'Directora María López',
        'comunidad_beneficiada' => 'Aldea El Progreso',
    ])->assertSessionHasNoErrors();

    $institucion = InstitucionReceptora::sole();

    expect($institucion)
        ->nombre->toBe('Escuela Oficial Rural Mixta El Progreso')
        ->tipo->toBe('Educación')
        ->correo_contacto->toBe('director@escuela.example')
        ->and($expediente->actores()->sole()->institucion_receptora_id)->toBe($institucion->id);
});

it('reutiliza la institución receptora ya registrada por otro estudiante', function () {
    $expediente = expedienteDelEstudiante();
    $existente = InstitucionReceptora::factory()->create(['nombre' => 'Centro de Salud Zona 5', 'tipo' => 'Salud']);
    $otroExpediente = Expediente::factory()->create();
    $otroExpediente->actores()->create([
        'institucion_receptora_id' => $existente->id,
        'contraparte' => 'Dr. Ramírez',
        'comunidad_beneficiada' => 'Zona 5',
    ]);

    $this->post(route('estudiante.expedientes.actores.store', $expediente), [
        'institucion_receptora_id' => $existente->id,
        'contraparte' => 'Enfermera Ortiz',
        'comunidad_beneficiada' => 'Zona 5',
    ])->assertSessionHasNoErrors();

    expect(InstitucionReceptora::count())->toBe(1)
        ->and($expediente->actores()->sole()->institucion_receptora_id)->toBe($existente->id);
});

it('no duplica una institución receptora al registrarla otra vez con el mismo nombre', function () {
    $expediente = expedienteDelEstudiante();
    $existente = InstitucionReceptora::factory()->create(['nombre' => 'Centro de Salud Zona 5', 'tipo' => 'Salud']);

    $this->post(route('estudiante.expedientes.actores.store', $expediente), [
        'institucion_nombre' => '  centro de salud zona 5 ',
        'institucion_tipo' => 'Otro tipo',
        'contraparte' => 'Enfermera Ortiz',
        'comunidad_beneficiada' => 'Zona 5',
    ])->assertSessionHasNoErrors();

    expect(InstitucionReceptora::count())->toBe(1)
        ->and($existente->fresh()->tipo)->toBe('Salud')
        ->and($expediente->actores()->sole()->institucion_receptora_id)->toBe($existente->id);
});

it('recupera una institución eliminada cuando se registra de nuevo por su nombre', function () {
    $expediente = expedienteDelEstudiante();
    $eliminada = InstitucionReceptora::factory()->create(['nombre' => 'Escuela Antigua']);
    $eliminada->delete();

    $this->post(route('estudiante.expedientes.actores.store', $expediente), [
        'institucion_nombre' => 'Escuela Antigua',
        'contraparte' => 'Director',
        'comunidad_beneficiada' => 'Barrio Norte',
    ])->assertSessionHasNoErrors();

    expect(InstitucionReceptora::count())->toBe(1)
        ->and($eliminada->fresh()->trashed())->toBeFalse();
});

it('exige elegir o registrar una institución receptora', function () {
    $expediente = expedienteDelEstudiante();

    $this->post(route('estudiante.expedientes.actores.store', $expediente), [
        'contraparte' => 'Director',
        'comunidad_beneficiada' => 'Barrio Norte',
    ])->assertSessionHasErrors(['institucion_nombre' => 'Selecciona una institución receptora o registra una nueva.']);
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
