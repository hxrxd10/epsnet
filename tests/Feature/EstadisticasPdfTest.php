<?php

use App\Enums\EstadoExpediente;
use App\Enums\TipoCambioBitacora;
use App\Models\Adjunto;
use App\Models\Bitacora;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\Municipio;
use App\Models\UnidadAcademica;
use App\Models\User;
use App\Support\MapaEstadisticoSvg;

/**
 * EPS válido con orden de impresión y ubicación, para que el PDF tenga algo que mostrar.
 */
function epsParaPdf(Departamento $departamento, ?UnidadAcademica $unidad = null): Expediente
{
    $expediente = Expediente::factory()->for(Estudiante::factory())->create([
        'estado_expediente' => EstadoExpediente::Verificado,
        'nombre_carrera' => 'Licenciatura en Pedagogía',
        ...($unidad === null ? [] : ['unidad_academica_id' => $unidad->id]),
    ]);
    Adjunto::factory()->create(['entidad_id' => $expediente->id, 'fecha_subida' => '2025-05-10 09:00:00']);
    $municipio = Municipio::firstOrCreate(['departamento_id' => $departamento->id, 'nombre' => 'Cabecera'], ['codigo' => $departamento->codigo.'01']);
    $expediente->ubicaciones()->create(['departamento_id' => $departamento->id, 'municipio_id' => $municipio->id]);
    $expediente->bienesServicios()->create(['tipo' => 'bien', 'descripcion' => 'Pupitres', 'cantidad_beneficiarios' => 30, 'fecha' => '2025-04-01']);

    return $expediente;
}

it('envía a los invitados a iniciar sesión', function () {
    $departamento = Departamento::factory()->create(['codigo' => '03']);

    $this->get(route('estadisticas.pdf'))->assertRedirect(route('login'));
    $this->get(route('estadisticas.departamento.pdf', $departamento))->assertRedirect(route('login'));
});

it('genera el PDF del país para cualquier rol con sesión', function (string $rol) {
    epsParaPdf(Departamento::factory()->create(['codigo' => '03', 'nombre' => 'Sacatepéquez']));
    $this->actingAs(User::factory()->{$rol}()->create());

    $respuesta = $this->get(route('estadisticas.pdf', ['metrica' => 'bienes_servicios']))->assertOk();

    expect($respuesta->headers->get('content-type'))->toBe('application/pdf')
        ->and($respuesta->headers->get('content-disposition'))->toContain('attachment')->toContain('estadisticas-epsnet-'.now()->format('Ymd').'.pdf')
        ->and(substr($respuesta->getContent(), 0, 5))->toBe('%PDF-');
})->with(['invitado', 'estudiante', 'administrador']);

it('genera el PDF de un departamento con el nombre de este en el archivo', function () {
    $sacatepequez = Departamento::factory()->create(['codigo' => '03', 'nombre' => 'Sacatepéquez']);
    epsParaPdf($sacatepequez);
    $this->actingAs(User::factory()->invitado()->create());

    $respuesta = $this->get(route('estadisticas.departamento.pdf', $sacatepequez))->assertOk();

    expect($respuesta->headers->get('content-type'))->toBe('application/pdf')
        ->and($respuesta->headers->get('content-disposition'))->toContain('estadisticas-sacatepequez-')
        ->and(substr($respuesta->getContent(), 0, 5))->toBe('%PDF-');
});

it('genera el PDF aunque no haya ningún dato con los filtros', function () {
    $this->actingAs(User::factory()->invitado()->create());
    $departamento = Departamento::factory()->create(['codigo' => '03']);

    $this->get(route('estadisticas.pdf'))->assertOk();
    $this->get(route('estadisticas.departamento.pdf', $departamento))->assertOk();
});

it('deja constancia en la bitácora de quién exportó, con los filtros aplicados', function () {
    $humanidades = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
    $sacatepequez = Departamento::factory()->create(['codigo' => '03', 'nombre' => 'Sacatepéquez']);
    epsParaPdf($sacatepequez, $humanidades);
    $usuario = User::factory()->invitado()->create(['email' => 'invitado@example.com']);
    $this->actingAs($usuario);

    $this->get(route('estadisticas.pdf', ['unidad' => $humanidades->id, 'anio' => 2025, 'carrera' => 'Licenciatura en Pedagogía', 'metrica' => 'eps']));
    $this->get(route('estadisticas.departamento.pdf', $sacatepequez));

    $registros = Bitacora::where('tipo_cambio', TipoCambioBitacora::Exportacion)->orderBy('id')->get();

    expect($registros)->toHaveCount(2)
        ->and($registros[0]->usuario_correo)->toBe('invitado@example.com')
        ->and($registros[0]->modulo)->toBe('Estadísticas')
        ->and($registros[0]->detalle)->toContain('estadísticas del país (EPS)')
        ->toContain('Año (orden de impresión): 2025')
        ->toContain('Unidad académica: Facultad de Humanidades')
        ->toContain('Carrera: Licenciatura en Pedagogía')
        ->and($registros[1]->detalle)->toContain('departamento Sacatepéquez')->toContain('Todos los años');
});

it('rechaza filtros y métricas inválidos', function () {
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route('estadisticas.pdf', ['anio' => 'abc']))->assertSessionHasErrors('anio');
    $this->get(route('estadisticas.pdf', ['metrica' => 'inventada']))->assertSessionHasErrors('metrica');
});

describe('mapa del PDF', function () {
    it('dibuja un punto por celda, más oscuro cuanto mayor es el valor', function () {
        $svg = MapaEstadisticoSvg::generar(['01' => 100, '02' => 25, '03' => 0]);

        expect(substr_count($svg, '<circle'))->toBe(3514)
            ->and($svg)->toContain('fill="#0F1031"')
            ->and($svg)->toContain('fill="#DDDFE8"')
            ->and($svg)->not->toContain('viewBox');
    });

    it('destaca solo el departamento indicado', function () {
        $svg = MapaEstadisticoSvg::generar([], '16');

        $oscuros = substr_count($svg, 'fill="#0F1031"');

        expect($oscuros)->toBeGreaterThan(200)->toBeLessThan(500)
            ->and(substr_count($svg, '<circle'))->toBe(3514)
            ->and(substr_count($svg, 'fill="#DDDFE8"'))->toBe(3514 - $oscuros);
    });

    it('se puede incrustar como imagen en el PDF', function () {
        expect(MapaEstadisticoSvg::dataUri(['01' => 1]))->toStartWith('data:image/svg+xml;base64,');
    });
});
