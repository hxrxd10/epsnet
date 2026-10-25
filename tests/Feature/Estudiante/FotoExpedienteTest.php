<?php

use App\Enums\EstadoExpediente;
use App\Models\Adjunto;
use App\Models\AdjuntoContenido;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

function expedienteConFoto(array $atributos = []): Expediente
{
    $estudiante = Estudiante::factory()->create();
    RegistroAcademicoFalso::iniciarSesion($estudiante);

    return Expediente::factory()->for($estudiante)->create($atributos);
}

function fotoDePrueba(string $nombre = 'jornada.jpg', int $ancho = 1200, int $alto = 800): UploadedFile
{
    return UploadedFile::fake()->image($nombre, $ancho, $alto);
}

it('guarda la foto del EPS en base64 en la base de datos y la muestra en el cierre', function () {
    $expediente = expedienteConFoto();
    $foto = fotoDePrueba();
    $bytes = $foto->get();

    $this->post(route('estudiante.expedientes.foto.store', $expediente), ['foto' => $foto])
        ->assertRedirect(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertSessionHasNoErrors();

    $adjunto = $expediente->foto()->sole();

    expect($adjunto)
        ->categoria->toBe(Adjunto::FOTO_EPS)
        ->mime_type->toBe('image/jpeg')
        ->sha256->toBe(hash('sha256', $bytes))
        ->and($adjunto->contenido->contenido)->toBe(base64_encode($bytes))
        ->and($expediente->ordenImpresion()->exists())->toBeFalse();

    $this->get(route('estudiante.expedientes.cierre.index', $expediente))
        ->assertInertia(fn (Assert $page) => $page->where('foto', $expediente->urlFoto()));
});

it('reemplaza la foto anterior y permite quitarla', function () {
    $expediente = expedienteConFoto();
    $this->post(route('estudiante.expedientes.foto.store', $expediente), ['foto' => fotoDePrueba('primera.jpg')]);
    $this->post(route('estudiante.expedientes.foto.store', $expediente), ['foto' => fotoDePrueba('segunda.png')]);

    expect($expediente->foto()->sole()->nombre_original)->toBe('segunda.png')
        ->and(AdjuntoContenido::count())->toBe(1);

    $this->delete(route('estudiante.expedientes.foto.destroy', $expediente))->assertRedirect();

    expect(Adjunto::count())->toBe(0)->and(AdjuntoContenido::count())->toBe(0);
});

it('no toca la orden de impresión al cambiar la foto', function () {
    $expediente = expedienteConFoto();
    Adjunto::factory()->for($expediente, 'entidad')->conContenido()->create();

    $this->post(route('estudiante.expedientes.foto.store', $expediente), ['foto' => fotoDePrueba()]);
    $this->delete(route('estudiante.expedientes.foto.destroy', $expediente));

    expect($expediente->ordenImpresion()->exists())->toBeTrue();
});

it('valida la foto', function (Closure $archivo, string $mensaje) {
    $expediente = expedienteConFoto();

    $this->post(route('estudiante.expedientes.foto.store', $expediente), ['foto' => $archivo()])
        ->assertSessionHasErrors(['foto' => $mensaje]);

    expect(Adjunto::count())->toBe(0);
})->with([
    'no es imagen' => [fn () => UploadedFile::fake()->create('foto.pdf', 10, 'application/pdf'), 'La foto debe ser una imagen JPG, PNG o WebP.'],
    'muy pesada' => [fn () => UploadedFile::fake()->image('foto.jpg', 1200, 800)->size(5121), 'La foto no puede pesar más de 5 MB.'],
    'muy pequeña' => [fn () => fotoDePrueba('foto.jpg', 120, 80), 'La foto es muy pequeña: debe medir al menos 300 × 200 píxeles.'],
]);

it('no permite cambiar la foto del EPS de otro estudiante', function () {
    expedienteConFoto();
    $ajeno = Expediente::factory()->create();
    Adjunto::factory()->for($ajeno, 'entidad')->conContenido()->create(['categoria' => Adjunto::FOTO_EPS]);

    $this->post(route('estudiante.expedientes.foto.store', $ajeno), ['foto' => fotoDePrueba()])->assertNotFound();
    $this->delete(route('estudiante.expedientes.foto.destroy', $ajeno))->assertNotFound();

    expect($ajeno->foto()->count())->toBe(1);
});

describe('quién ve la foto', function () {
    /**
     * EPS con su foto guardada, en el estado indicado.
     */
    function epsConFotoGuardada(EstadoExpediente $estado): Expediente
    {
        $expediente = Expediente::factory()->create(['estado_expediente' => $estado]);
        Adjunto::factory()->for($expediente, 'entidad')->conContenido('bytes-de-la-foto')->create([
            'categoria' => Adjunto::FOTO_EPS,
            'mime_type' => 'image/jpeg',
            'sha256' => hash('sha256', 'bytes-de-la-foto'),
        ]);

        return $expediente;
    }

    it('la muestra a quien puede consultar el repositorio cuando el EPS está aprobado, pero no a un invitado', function () {
        $expediente = epsConFotoGuardada(EstadoExpediente::Verificado);
        $this->actingAs(User::factory()->invitado()->create())->get(route('expedientes.foto', $expediente))->assertNotFound();
        $this->actingAs(User::factory()->estudiante()->create());

        $respuesta = $this->get(route('expedientes.foto', $expediente))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');

        expect($respuesta->getContent())->toBe('bytes-de-la-foto');
    });

    it('la oculta a terceros mientras el EPS no está aprobado, pero no a su estudiante ni a DIGEU', function () {
        $expediente = epsConFotoGuardada(EstadoExpediente::Completo);

        $this->actingAs(User::factory()->invitado()->create())->get(route('expedientes.foto', $expediente))->assertNotFound();
        $this->actingAs(User::factory()->administrador()->create())->get(route('expedientes.foto', $expediente))->assertOk();

        RegistroAcademicoFalso::iniciarSesion($expediente->estudiante);
        $this->get(route('expedientes.foto', $expediente))->assertOk();
    });

    it('envía a los invitados sin sesión a iniciar sesión', function () {
        $expediente = epsConFotoGuardada(EstadoExpediente::Verificado);

        $this->get(route('expedientes.foto', $expediente))->assertRedirect(route('login'));
    });

    it('aparece en el repositorio con su dirección', function () {
        $expediente = epsConFotoGuardada(EstadoExpediente::Verificado);
        $this->actingAs(User::factory()->administrador()->create());

        $this->get(route('repositorio.index'))
            ->assertInertia(fn (Assert $page) => $page->where('expedientes.0.foto', $expediente->urlFoto()));
        $this->get(route('repositorio.show', $expediente))
            ->assertInertia(fn (Assert $page) => $page->where('expediente.foto', $expediente->urlFoto()));
    });
});
