<?php

use App\Enums\EstadoExpediente;
use App\Models\Adjunto;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\User;
use Database\Seeders\DatosDemoSeeder;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

it('siembra EPS de demostración repartidos por el país y por años', function () {
    $this->seed(DatosDemoSeeder::class);

    $porAnio = DB::table('adjuntos')->where('categoria', Adjunto::ORDEN_IMPRESION)
        ->selectRaw('substr(fecha_subida, 1, 4) as anio, count(*) as total')->groupBy('anio')->pluck('total', 'anio');

    expect(Expediente::count())->toBe(320)
        ->and(Estudiante::where('carnet', 'like', 'DEMO-%')->count())->toBe(320)
        ->and(Expediente::where('estado_expediente', EstadoExpediente::Verificado)->count())->toBeGreaterThan(150)
        ->and(DB::table('ubicaciones_territoriales')->distinct()->count('departamento_id'))->toBe(22)
        ->and($porAnio->keys()->all())->toBe([2023, 2024, 2025, 2026])
        ->and(DB::table('unidades_academicas')->whereNull('latitud')->count())->toBe(0)
        ->and(DB::table('ubicaciones_territoriales')->whereNull('municipio_id')->count())->toBe(0);
});

it('solo da orden de impresión a los EPS completos o verificados', function () {
    $this->seed(DatosDemoSeeder::class);

    expect(Expediente::validos()->whereDoesntHave('ordenImpresion')->count())->toBe(0)
        ->and(Expediente::where('estado_expediente', EstadoExpediente::Activo)->whereHas('ordenImpresion')->count())->toBe(0);
});

it('no duplica los datos al sembrar de nuevo', function () {
    $this->seed(DatosDemoSeeder::class);
    $this->seed(DatosDemoSeeder::class);

    expect(Expediente::count())->toBe(320);
});

it('crea las cuentas de prueba de cada rol', function () {
    $this->seed(DatosDemoSeeder::class);

    expect(User::where('email', 'admin@epsnet.test')->sole()->esAdministrador())->toBeTrue()
        ->and(User::where('email', 'unidad@epsnet.test')->sole()->unidadAcademica->nombre)->toBe('Facultad de Humanidades')
        ->and(User::where('email', 'invitado@epsnet.test')->sole()->rol->clave)->toBe('invitado');
});

it('alimenta las estadísticas con datos en todos los ejes', function () {
    $this->seed(DatosDemoSeeder::class);
    $this->actingAs(User::where('email', 'invitado@epsnet.test')->sole());

    $this->get(route('estadisticas.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('totales.eps', fn (int $eps): bool => $eps > 200)
            ->where('totales.investigaciones', fn (int $total): bool => $total > 50)
            ->where('totales.bienes_servicios', fn (int $total): bool => $total > 500)
            ->where('totales.acciones', fn (int $total): bool => $total > 200)
            ->where('totales.instituciones', fn (int $total): bool => $total > 50)
            ->where('anios', [2026, 2025, 2024, 2023])
            ->has('unidades', 22)
            ->where('departamentos', fn ($departamentos) => collect($departamentos)->every(fn (array $departamento): bool => $departamento['metricas']['eps'] > 0)));
});

it('no siembra en producción', function () {
    app()->detectEnvironment(fn (): string => 'production');

    app(DatosDemoSeeder::class)->run();

    expect(Expediente::count())->toBe(0);
});

it('deja una bitácora de ejemplo sin anotar la propia siembra', function () {
    $this->seed(DatosDemoSeeder::class);

    expect(DB::table('bitacoras')->count())->toBeGreaterThan(150)
        ->and(DB::table('bitacoras')->where('fecha_hora', '>=', now()->subMinutes(5))->count())->toBe(0)
        ->and(DB::table('bitacoras')->where('tipo_cambio', 'aprobacion')->whereNotNull('usuario_correo')->count())->toBeGreaterThan(20)
        ->and(User::where('email', 'like', 'estudiante%@epsnet.test')->count())->toBe(8);
});
