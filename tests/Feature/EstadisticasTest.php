<?php

use App\Enums\EstadoExpediente;
use App\Models\Adjunto;
use App\Models\Catalogo;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use App\Models\Municipio;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * EPS aprobado (válido para las estadísticas) con su orden de impresión fechada y su primera ubicación.
 *
 * @param  array<string, mixed>  $atributos
 */
function epsEstadistico(Departamento $departamento, string $municipio, string $fechaOrden = '2026-03-10', array $atributos = []): Expediente
{
    $expediente = Expediente::factory()
        ->for(Estudiante::factory())
        ->create([
            'estado_expediente' => EstadoExpediente::Verificado,
            'verificado_at' => "{$fechaOrden} 10:00:00",
            'nombre_carrera' => 'Licenciatura en Pedagogía',
            ...$atributos,
        ]);
    Adjunto::factory()->create(['entidad_id' => $expediente->id, 'fecha_subida' => "{$fechaOrden} 09:00:00"]);
    $catalogo = Municipio::firstOrCreate(
        ['departamento_id' => $departamento->id, 'nombre' => $municipio],
        ['codigo' => fake()->unique()->numerify('####')],
    );
    $expediente->ubicaciones()->create(['departamento_id' => $departamento->id, 'municipio_id' => $catalogo->id]);

    return $expediente;
}

function departamentoEstadistico(string $codigo, string $nombre): Departamento
{
    return Departamento::factory()->create(['codigo' => $codigo, 'nombre' => $nombre]);
}

it('envía a los invitados a iniciar sesión', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');

    $this->get(route('estadisticas.index'))->assertRedirect(route('login'));
    $this->get(route('estadisticas.departamento', $departamento))->assertRedirect(route('login'));
});

it('muestra las estadísticas a DIGEU, a las unidades académicas y a los estudiantes', function (string $rol) {
    $this->actingAs(User::factory()->{$rol}()->create());

    $this->get(route('estadisticas.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('estadisticas/index'));
})->with(['estudiante', 'administrador', 'unidadAcademica']);

it('no da acceso a las consultas a los invitados', function (string $ruta, array $parametros) {
    Departamento::factory()->create(['codigo' => '03']);
    $this->actingAs(User::factory()->invitado()->create());

    $this->get(route($ruta, $parametros))->assertForbidden();
})->with([
    'estadísticas' => ['estadisticas.index', []],
    'departamento' => ['estadisticas.departamento', ['departamento' => '03']],
    'PDF' => ['estadisticas.pdf', []],
    'Excel' => ['estadisticas.excel', []],
    'ODS' => ['ods.index', []],
    'Excel de ODS' => ['ods.excel', []],
    'repositorio' => ['repositorio.index', []],
    'investigaciones' => ['investigaciones.index', []],
]);

it('suma por departamento lo registrado en cada eje', function () {
    $sacatepequez = departamentoEstadistico('03', 'Sacatepéquez');
    $peten = departamentoEstadistico('17', 'Petén');
    $mobiliario = Catalogo::factory()->create(['nombre' => 'Mobiliario escolar', 'valor_base' => 350]);
    $ministerio = InstitucionAliada::factory()->create(['nombre' => 'Ministerio de Educación']);
    $ong = InstitucionAliada::factory()->create(['nombre' => 'Cruz Roja']);

    $uno = epsEstadistico($sacatepequez, 'Antigua Guatemala');
    $uno->bienesServicios()->create(['tipo' => 'bien', 'catalogo_id' => $mobiliario->id, 'descripcion' => 'Pupitres', 'cantidad_beneficiarios' => 40, 'fecha' => '2026-04-01']);
    $uno->bienesServicios()->create(['tipo' => 'bien', 'catalogo_id' => $mobiliario->id, 'descripcion' => 'Sillas', 'cantidad_beneficiarios' => 10, 'fecha' => '2026-04-02']);
    $uno->transferencias()->create(['tipo_actividad' => 'taller', 'actividad' => 'Taller de lectura', 'comunidad' => 'San Felipe', 'numero_participantes' => 25, 'fecha' => '2026-05-01']);
    $uno->publicaciones()->create(['titulo' => 'Lectura temprana', 'tipo' => 'articulo', 'autores' => 'A. Pérez']);
    $uno->actores()->create(['institucion_receptora' => 'Escuela de San Felipe', 'contraparte' => 'Director', 'comunidad_beneficiada' => 'San Felipe']);
    $uno->alianzas()->create(['institucion_aliada_id' => $ministerio->id]);
    $uno->alianzas()->create(['institucion_aliada_id' => $ong->id]);
    $dos = epsEstadistico($sacatepequez, 'Jocotenango');
    $dos->actores()->create(['institucion_receptora' => 'Escuela de Jocotenango', 'contraparte' => 'Directora', 'comunidad_beneficiada' => 'Jocotenango']);
    $dos->alianzas()->create(['institucion_aliada_id' => $ministerio->id]);
    epsEstadistico($peten, 'Flores')->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada', 'cantidad_beneficiarios' => 5, 'fecha' => '2026-04-03']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('totales.eps', 3)
            ->where('totales.bienes_servicios', 3)
            ->where('totales.beneficiarios', 55)
            ->where('departamentos', fn ($lista) => collect($lista)->firstWhere('codigo', '03') === [
                'codigo' => '03',
                'nombre' => 'Sacatepéquez',
                'cabecera' => $sacatepequez->cabecera,
                'metricas' => [
                    'eps' => 2,
                    'estudiantes' => 2,
                    'bienes_servicios' => 2,
                    'aporte' => 700,
                    'beneficiarios' => 50,
                    'acciones' => 1,
                    'participantes' => 25,
                    'investigaciones' => 1,
                    'instituciones' => 2,
                ],
                'top_bienes_servicios' => [['nombre' => 'Mobiliario escolar', 'tipo' => 'bien', 'cantidad' => 2, 'beneficiarios' => 50, 'aporte' => 700]],
            ]));
});

it('calcula el aporte en quetzales: la cantidad de cada bien o servicio por su valor base', function () {
    $sacatepequez = departamentoEstadistico('03', 'Sacatepéquez');
    $pupitres = Catalogo::factory()->create(['nombre' => 'Pupitres', 'valor_base' => 350.50]);
    $jornada = Catalogo::factory()->create(['nombre' => 'Jornada médica', 'categoria' => 'servicio', 'valor_base' => 1200]);
    $sinValor = Catalogo::factory()->create(['nombre' => 'Asesoría', 'categoria' => 'servicio']);

    $eps = epsEstadistico($sacatepequez, 'Antigua Guatemala');
    $eps->bienesServicios()->create(['tipo' => 'bien', 'catalogo_id' => $pupitres->id, 'cantidad' => 40, 'descripcion' => 'Pupitres', 'fecha' => '2026-04-01']);
    $eps->bienesServicios()->create(['tipo' => 'servicio', 'catalogo_id' => $jornada->id, 'descripcion' => 'Sin cantidad: cuenta como una', 'fecha' => '2026-04-02']);
    $eps->bienesServicios()->create(['tipo' => 'servicio', 'catalogo_id' => $sinValor->id, 'cantidad' => 5, 'descripcion' => 'Sin valor base', 'fecha' => '2026-04-03']);
    $eps->bienesServicios()->create(['tipo' => 'bien', 'cantidad' => 3, 'descripcion' => 'Fuera del catálogo', 'fecha' => '2026-04-04']);
    epsEstadistico($sacatepequez, 'Antigua Guatemala', atributos: ['estado_expediente' => EstadoExpediente::Completo])
        ->bienesServicios()->create(['tipo' => 'bien', 'catalogo_id' => $pupitres->id, 'cantidad' => 100, 'descripcion' => 'Aún en revisión', 'fecha' => '2026-04-05']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('totales.aporte', 15220)
            ->where('totales.bienes_servicios', 49)
            ->where('departamentos', fn ($lista) => collect($lista)->firstWhere('codigo', '03')['metricas']['aporte'] === 15220));

    $this->get(route('estadisticas.departamento', $sacatepequez))
        ->assertInertia(fn (Assert $page) => $page
            ->where('metricas.aporte', 15220)
            ->where('municipios.0.bienes_servicios.0.nombre', 'Pupitres')
            ->where('municipios.0.bienes_servicios.0.aporte', 14020));
});

it('atribuye cada EPS solo a su primera ubicación y aparta los que no tienen', function () {
    $sacatepequez = departamentoEstadistico('03', 'Sacatepéquez');
    $peten = departamentoEstadistico('17', 'Petén');
    $conDos = epsEstadistico($sacatepequez, 'Antigua Guatemala');
    $conDos->ubicaciones()->create(['departamento_id' => $peten->id, 'municipio_id' => Municipio::factory()->for($peten)->create()->id]);
    $conDos->publicaciones()->create(['titulo' => 'Estudio', 'tipo' => 'tesis', 'autores' => 'B. López']);
    $sinUbicacion = Expediente::factory()->create(['estado_expediente' => EstadoExpediente::Verificado]);
    $sinUbicacion->publicaciones()->create(['titulo' => 'Otro', 'tipo' => 'tesis', 'autores' => 'C. Ruiz']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('totales.investigaciones', 2)
            ->where('sin_ubicacion.investigaciones', 1)
            ->where('departamentos', fn ($lista) => collect($lista)->pluck('metricas.investigaciones', 'codigo')->only(['03', '17'])->all() === ['03' => 1, '17' => 0]));
});

it('cuenta solo los EPS aprobados por supervisión', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['estado_expediente' => EstadoExpediente::Verificado]);
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['estado_expediente' => EstadoExpediente::Completo, 'verificado_at' => null, 'aprobacion_solicitada_at' => now()]);
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['estado_expediente' => EstadoExpediente::Activo, 'verificado_at' => null]);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index'))->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1));
});

it('filtra por el año de la orden de impresión y ofrece los años con EPS', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');
    epsEstadistico($departamento, 'Antigua Guatemala', '2024-12-31');
    epsEstadistico($departamento, 'Antigua Guatemala', '2025-01-01');
    epsEstadistico($departamento, 'Antigua Guatemala', '2026-06-15');
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index'))
        ->assertInertia(fn (Assert $page) => $page->where('totales.eps', 3)->where('anios', [2026, 2025, 2024]));
    $this->get(route('estadisticas.index', ['anio' => 2025]))
        ->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1)->where('filtros.anio', '2025'));
});

it('filtra por unidad académica y por carrera, limitando las carreras a la unidad', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');
    $humanidades = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
    $ingenieria = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Ingeniería']);
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['unidad_academica_id' => $humanidades->id, 'nombre_carrera' => 'Pedagogía']);
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['unidad_academica_id' => $humanidades->id, 'nombre_carrera' => 'Psicología']);
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['unidad_academica_id' => $ingenieria->id, 'nombre_carrera' => 'Ingeniería Civil']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('carreras', fn ($carreras) => collect($carreras)->pluck('value')->all() === ['Ingeniería Civil', 'Pedagogía', 'Psicología'])
            ->has('unidades', 2));
    $this->get(route('estadisticas.index', ['unidad' => $humanidades->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->where('totales.eps', 2)
            ->where('carreras', fn ($carreras) => collect($carreras)->pluck('value')->all() === ['Pedagogía', 'Psicología']));
    $this->get(route('estadisticas.index', ['unidad' => $humanidades->id, 'carrera' => 'Psicología']))
        ->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1)->where('filtros.carrera', 'Psicología'));
});

it('descarta la carrera que no pertenece a la unidad elegida', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');
    $humanidades = UnidadAcademica::factory()->create();
    $ingenieria = UnidadAcademica::factory()->create();
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['unidad_academica_id' => $humanidades->id, 'nombre_carrera' => 'Pedagogía']);
    epsEstadistico($departamento, 'Antigua Guatemala', atributos: ['unidad_academica_id' => $ingenieria->id, 'nombre_carrera' => 'Ingeniería Civil']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index', ['unidad' => $humanidades->id, 'carrera' => 'Ingeniería Civil']))
        ->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1)->where('filtros.carrera', ''));
});

it('rechaza filtros inválidos', function () {
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index', ['anio' => 'abc', 'unidad' => 9999]))
        ->assertSessionHasErrors(['anio', 'unidad']);
});

it('organiza el departamento por municipio con bienes y servicios e investigaciones', function () {
    $sacatepequez = departamentoEstadistico('03', 'Sacatepéquez');
    $peten = departamentoEstadistico('17', 'Petén');
    $mobiliario = Catalogo::factory()->create(['nombre' => 'Mobiliario escolar']);
    $salud = Catalogo::factory()->create(['nombre' => 'Jornada médica']);

    $antigua = epsEstadistico($sacatepequez, 'Antigua Guatemala', atributos: ['nombre_carrera' => 'Pedagogía', 'nombre_unidad' => 'Facultad de Humanidades']);
    $antigua->bienesServicios()->create(['tipo' => 'bien', 'catalogo_id' => $mobiliario->id, 'descripcion' => 'Pupitres', 'cantidad_beneficiarios' => 40, 'fecha' => '2026-04-01']);
    $antigua->bienesServicios()->create(['tipo' => 'servicio', 'catalogo_id' => $salud->id, 'descripcion' => 'Jornada', 'cantidad_beneficiarios' => 30, 'fecha' => '2026-04-02']);
    $antigua->bienesServicios()->create(['tipo' => 'servicio', 'catalogo_id' => $salud->id, 'descripcion' => 'Otra jornada', 'cantidad_beneficiarios' => 20, 'fecha' => '2026-04-03']);
    $antigua->publicaciones()->create(['titulo' => 'Lectura temprana', 'tipo' => 'articulo', 'autores' => 'A. Pérez', 'medio_publicacion' => 'Revista USAC', 'fecha_publicacion' => '2026-07-01']);
    $mismo = epsEstadistico($sacatepequez, 'Antigua Guatemala');
    $mismo->publicaciones()->create(['titulo' => 'Otra lectura', 'tipo' => 'tesis', 'autores' => 'B. López']);
    epsEstadistico($sacatepequez, 'Jocotenango');
    epsEstadistico($peten, 'Flores')->publicaciones()->create(['titulo' => 'De Petén', 'tipo' => 'informe', 'autores' => 'C. Ruiz']);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.departamento', $sacatepequez))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('estadisticas/departamento')
            ->where('departamento.nombre', 'Sacatepéquez')
            ->where('metricas.eps', 3)
            ->where('metricas.investigaciones', 2)
            ->where('municipios', fn ($municipios) => collect($municipios)->pluck('nombre')->all() === ['Antigua Guatemala', 'Jocotenango'])
            ->where('municipios.0.metricas.eps', 2)
            ->where('municipios.0.bienes_servicios', [
                ['nombre' => 'Jornada médica', 'tipo' => 'servicio', 'cantidad' => 2, 'beneficiarios' => 50, 'aporte' => 0],
                ['nombre' => 'Mobiliario escolar', 'tipo' => 'bien', 'cantidad' => 1, 'beneficiarios' => 40, 'aporte' => 0],
            ])
            ->where('municipios.0.investigaciones.0.titulo', 'Lectura temprana')
            ->where('municipios.0.investigaciones.0.tipo', 'Artículo')
            ->where('municipios.0.investigaciones.0.carrera', 'Pedagogía')
            ->where('municipios.0.investigaciones.0.unidad', 'Facultad de Humanidades')
            ->has('municipios.0.investigaciones', 2)
            ->has('municipios.1.investigaciones', 0));
});

it('aplica los filtros también en la página del departamento', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');
    epsEstadistico($departamento, 'Antigua Guatemala', '2025-05-01');
    epsEstadistico($departamento, 'Antigua Guatemala', '2026-05-01');
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.departamento', [$departamento, 'anio' => 2026]))
        ->assertInertia(fn (Assert $page) => $page->where('metricas.eps', 1));
});

it('agrupa aparte las ubicaciones anteriores al catálogo, que no tienen municipio', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');
    epsEstadistico($departamento, 'Antigua Guatemala');
    $antiguo = epsEstadistico($departamento, 'Jocotenango');
    $antiguo->ubicaciones()->update(['municipio_id' => null]);
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.departamento', $departamento))
        ->assertInertia(fn (Assert $page) => $page
            ->where('municipios', fn ($municipios) => collect($municipios)->pluck('nombre')->all() === ['Antigua Guatemala', 'Sin municipio']));
});

it('toma el año de la aprobación cuando el EPS no tiene orden de impresión', function () {
    $departamento = departamentoEstadistico('03', 'Sacatepéquez');
    $conOrden = epsEstadistico($departamento, 'Antigua Guatemala', '2025-03-10');
    $sinOrden = epsEstadistico($departamento, 'Antigua Guatemala', '2023-01-01', ['estado_expediente' => EstadoExpediente::Verificado, 'verificado_at' => '2024-06-15 10:00:00']);
    $sinOrden->ordenImpresion()->delete();
    $sinOrdenNiAprobacion = epsEstadistico($departamento, 'Antigua Guatemala', '2023-01-01', ['verificado_at' => null]);
    $sinOrdenNiAprobacion->ordenImpresion()->delete();
    $this->actingAs(User::factory()->administrador()->create());

    $this->get(route('estadisticas.index'))
        ->assertInertia(fn (Assert $page) => $page->where('totales.eps', 3)->where('anios', [2025, 2024]));
    $this->get(route('estadisticas.index', ['anio' => 2024]))
        ->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1));
    $this->get(route('estadisticas.index', ['anio' => 2025]))
        ->assertInertia(fn (Assert $page) => $page->where('totales.eps', 1));
    expect($conOrden->exists)->toBeTrue();
});
