<?php

use App\Enums\EstadoExpediente;
use App\Models\Adjunto;
use App\Models\AdjuntoContenido;
use App\Models\Estudiante;
use App\Models\Expediente;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

function expedienteConRegistros(): Expediente
{
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);
    $expediente = Expediente::factory()->for($estudiante)->create();
    $expediente->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada de alfabetización', 'fecha' => '2026-03-10']);

    return $expediente;
}

const CONTENIDO_PDF = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF";

function ordenPdf(string $nombre = 'orden-de-impresion.pdf', string $contenido = CONTENIDO_PDF): UploadedFile
{
    return UploadedFile::fake()->createWithContent($nombre, $contenido);
}

function subirOrden(Expediente $expediente, ?UploadedFile $archivo = null): TestResponse
{
    return test()->post(route('estudiante.expedientes.orden-impresion.store', $expediente), [
        'orden_impresion' => $archivo ?? ordenPdf(),
    ]);
}

it('muestra el resumen de lo registrado en cada eje', function () {
    $expediente = expedienteConRegistros();
    $expediente->publicaciones()->create(['titulo' => 'Impacto de la lectura', 'tipo' => 'articulo', 'autores' => 'Madeley Morales']);

    $this->get(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('estudiante/pasos/cierre')
            ->where('expediente.estado', 'activo')
            ->where('eje', 7)
            ->has('resumen', 6)
            ->where('resumen.0.items', ['Jornada de alfabetización'])
            ->where('resumen.1.items', ['Impacto de la lectura'])
            ->where('resumen.2.items', [])
            ->where('total_registros', 2)
            ->where('orden_impresion', null));
});

it('guarda la orden de impresión en base64 en la base de datos', function () {
    $expediente = expedienteConRegistros();

    subirOrden($expediente, ordenPdf('mi-orden.pdf'))
        ->assertRedirect(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertSessionHasNoErrors();

    $adjunto = $expediente->ordenImpresion()->sole();

    expect($adjunto)
        ->categoria->toBe(Adjunto::ORDEN_IMPRESION)
        ->nombre_original->toBe('mi-orden.pdf')
        ->mime_type->toBe('application/pdf')
        ->tamano_bytes->toBe(strlen(CONTENIDO_PDF))
        ->sha256->toBe(hash('sha256', CONTENIDO_PDF))
        ->ruta_archivo->toBeNull()
        ->subido_por->toBe(auth()->id())
        ->and($adjunto->contenido->contenido)->toBe(base64_encode(CONTENIDO_PDF))
        ->and($adjunto->contenido->bytes())->toBe(CONTENIDO_PDF);
});

it('muestra los datos de la orden de impresión ya subida', function () {
    $expediente = expedienteConRegistros();
    subirOrden($expediente, ordenPdf('mi-orden.pdf'));

    $this->get(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page
            ->where('orden_impresion.nombre', 'mi-orden.pdf')
            ->where('orden_impresion.descarga', route('estudiante.expedientes.orden-impresion.show', $expediente, absolute: false)));
});

it('reemplaza la orden de impresión y elimina el contenido anterior', function () {
    $expediente = expedienteConRegistros();
    subirOrden($expediente, ordenPdf('primera.pdf', '%PDF-1.4 primera'));

    subirOrden($expediente, ordenPdf('segunda.pdf', '%PDF-1.4 segunda'));

    $adjunto = $expediente->ordenImpresion()->sole();

    expect($adjunto->nombre_original)->toBe('segunda.pdf')
        ->and($adjunto->contenido->bytes())->toBe('%PDF-1.4 segunda')
        ->and(Adjunto::count())->toBe(1)
        ->and(AdjuntoContenido::count())->toBe(1);
});

it('valida el archivo de la orden de impresión', function (?Closure $archivo, string $mensaje) {
    $expediente = expedienteConRegistros();

    $this->post(route('estudiante.expedientes.orden-impresion.store', $expediente), ['orden_impresion' => $archivo?->call($this)])
        ->assertSessionHasErrors(['orden_impresion' => $mensaje]);

    expect(Adjunto::count())->toBe(0)
        ->and(AdjuntoContenido::count())->toBe(0);
})->with([
    'sin archivo' => [null, 'Selecciona el archivo de tu orden de impresión.'],
    'tipo no permitido' => [fn () => UploadedFile::fake()->create('orden.exe', 10, 'application/octet-stream'), 'La orden de impresión debe ser un PDF o una imagen (JPG o PNG).'],
    'muy pesado' => [fn () => UploadedFile::fake()->create('orden.pdf', 10241, 'application/pdf'), 'La orden de impresión no puede pesar más de 10 MB.'],
]);

it('acepta imágenes como orden de impresión', function (string $nombre) {
    $expediente = expedienteConRegistros();

    subirOrden($expediente, UploadedFile::fake()->image($nombre, 800, 600))->assertSessionHasNoErrors();

    expect($expediente->ordenImpresion()->sole()->nombre_original)->toBe($nombre);
})->with(['orden.jpg', 'orden.png']);

it('no completa ni aprueba el EPS solo con la orden de impresión', function () {
    $expediente = expedienteConRegistros();

    subirOrden($expediente)->assertSessionHasNoErrors();

    expect($expediente->fresh())
        ->estado_expediente->toBe(EstadoExpediente::Activo)
        ->aprobacion_solicitada_at->toBeNull()
        ->and(Expediente::validos()->count())->toBe(0);
});

it('mantiene en revisión el EPS al eliminar su orden de impresión', function () {
    $expediente = expedienteConRegistros();
    subirOrden($expediente);
    $expediente->enviarAAprobacion();

    $this->delete(route('estudiante.expedientes.orden-impresion.destroy', $expediente))
        ->assertRedirect(route('estudiante.expedientes.cierre.index', $expediente));

    expect($expediente->fresh())
        ->estado_expediente->toBe(EstadoExpediente::Completo)
        ->aprobacion_solicitada_at->not->toBeNull()
        ->and(Adjunto::count())->toBe(0)
        ->and(AdjuntoContenido::count())->toBe(0);
});

it('devuelve a la bandeja un EPS aprobado al cambiar su orden de impresión', function (Closure $cambio) {
    $expediente = expedienteConRegistros();
    subirOrden($expediente);
    $expediente->update([
        'estado_expediente' => EstadoExpediente::Verificado,
        'completado_at' => now()->subWeek(),
        'verificado_at' => now()->subDay(),
    ]);

    $cambio->call($this, $expediente);

    expect($expediente->fresh())
        ->estado_expediente->toBe(EstadoExpediente::Completo)
        ->verificado_at->toBeNull()
        ->aprobacion_solicitada_at->not->toBeNull()
        ->and(Expediente::validos()->count())->toBe(0);
})->with([
    'al reemplazarla' => [fn (Expediente $expediente) => subirOrden($expediente, ordenPdf('nueva.pdf'))],
    'al eliminarla' => [fn (Expediente $expediente) => $this->delete(route('estudiante.expedientes.orden-impresion.destroy', $expediente))],
]);

it('considera válidos para las estadísticas solo los EPS aprobados', function () {
    Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Activo]);
    Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Completo]);
    $aprobado = Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Verificado]);

    expect(Expediente::validos()->pluck('id')->all())->toBe([$aprobado->id]);
});

it('permite descargar la orden de impresión guardada, con su contenido original', function () {
    $expediente = expedienteConRegistros();
    subirOrden($expediente, ordenPdf('mi-orden.pdf'));

    $respuesta = $this->get(route('estudiante.expedientes.orden-impresion.show', $expediente))
        ->assertOk()
        ->assertDownload('mi-orden.pdf')
        ->assertHeader('Content-Type', 'application/pdf');

    expect($respuesta->streamedContent())->toBe(CONTENIDO_PDF);
});

it('responde 404 al descargar una orden de impresión sin contenido', function () {
    $expediente = expedienteConRegistros();
    Adjunto::factory()->for($expediente, 'entidad')->create();

    $this->get(route('estudiante.expedientes.orden-impresion.show', $expediente))->assertNotFound();
});

it('responde 404 al descargar una orden de impresión que no existe', function () {
    $expediente = expedienteConRegistros();

    $this->get(route('estudiante.expedientes.orden-impresion.show', $expediente))->assertNotFound();
});

it('oculta el cierre y la orden de impresión de otros estudiantes', function () {
    $ajeno = Expediente::factory()->create();
    Adjunto::factory()->for($ajeno, 'entidad')->conContenido()->create();
    RegistroAcademicoFalso::iniciarSesion(Estudiante::factory()->create());

    $this->get(route('estudiante.expedientes.cierre.index', $ajeno))->assertNotFound();
    $this->post(route('estudiante.expedientes.solicitud.store', $ajeno))->assertNotFound();
    $this->get(route('estudiante.expedientes.orden-impresion.show', $ajeno))->assertNotFound();
    $this->post(route('estudiante.expedientes.orden-impresion.store', $ajeno), ['orden_impresion' => ordenPdf()])->assertNotFound();
    $this->delete(route('estudiante.expedientes.orden-impresion.destroy', $ajeno))->assertNotFound();

    expect(Adjunto::count())->toBe(1)
        ->and($ajeno->fresh()->estado_expediente)->toBe(EstadoExpediente::Activo);
});

it('envía a los invitados a iniciar sesión', function () {
    $expediente = Expediente::factory()->create();

    $this->get(route('estudiante.expedientes.cierre.index', $expediente))->assertRedirect(route('login'));
    $this->get(route('estudiante.expedientes.orden-impresion.show', $expediente))->assertRedirect(route('login'));
});
