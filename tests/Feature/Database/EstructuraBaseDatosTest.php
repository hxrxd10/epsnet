<?php

use App\Models\BienServicio;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\InstitucionReceptora;
use App\Models\PublicacionInvestigacion;
use App\Models\UnidadAcademica;
use App\Models\User;
use Database\Seeders\DepartamentoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('crea todas las tablas del modelo de datos', function (string $tabla) {
    expect(Schema::hasTable($tabla))->toBeTrue();
})->with([
    'roles', 'departamentos', 'unidades_academicas', 'estudiantes', 'expedientes',
    'formularios_ingreso', 'bienes_servicios', 'publicaciones_investigacion',
    'transferencias_conocimiento', 'ubicaciones_territoriales',
    'instituciones_receptoras', 'actores_participantes', 'seguimientos_impacto',
    'bitacoras', 'adjuntos', 'adjunto_contenidos', 'catalogos',
]);

it('agrega el rol y el estado activo a los usuarios', function () {
    expect(Schema::hasColumns('users', ['rol_id', 'activo']))->toBeTrue();
});

it('usa columnas de texto largo para títulos y contenidos', function (string $tabla, string $columna) {
    expect(Schema::getColumnType($tabla, $columna))->toBe('text');
})->with([
    ['publicaciones_investigacion', 'titulo'],
    ['publicaciones_investigacion', 'resumen'],
    ['bienes_servicios', 'descripcion'],
    ['transferencias_conocimiento', 'actividad'],
    ['seguimientos_impacto', 'avance'],
    ['seguimientos_impacto', 'evaluacion_impacto'],
]);

it('guarda contenidos de unas 4000 palabras sin truncarlos', function () {
    $texto = implode(' ', array_fill(0, 4000, 'investigación'));

    $publicacion = PublicacionInvestigacion::factory()->create(['titulo' => $texto, 'resumen' => $texto]);

    expect($publicacion->fresh())
        ->titulo->toBe($texto)
        ->resumen->toBe($texto);
});

it('siembra los 22 departamentos de Guatemala sin duplicarlos al repetir la siembra', function () {
    $this->seed(DepartamentoSeeder::class);
    $this->seed(DepartamentoSeeder::class);

    expect(DB::table('departamentos')->count())->toBe(22)
        ->and(DB::table('departamentos')->where('nombre', 'Quetzaltenango')->value('cabecera'))->toBe('Quetzaltenango');
});

it('siembra los cuatro roles del sistema sin duplicarlos al repetir la siembra', function () {
    $this->seed(RolSeeder::class);
    $this->seed(RolSeeder::class);

    expect(DB::table('roles')->orderBy('clave')->pluck('clave')->all())->toBe(['digeu', 'estudiante', 'invitado', 'unidad_academica']);
});

it('impide registrar dos estudiantes con el mismo carné', function () {
    Estudiante::factory()->create(['carnet' => '202012345']);

    Estudiante::factory()->create(['carnet' => '202012345']);
})->throws(QueryException::class);

it('impide vincular un mismo usuario a dos estudiantes', function () {
    $usuario = User::factory()->create();

    Estudiante::factory()->create(['usuario_id' => $usuario->id]);
    Estudiante::factory()->create(['usuario_id' => $usuario->id]);
})->throws(QueryException::class);

it('permite estudiantes sin usuario del sistema', function () {
    Estudiante::factory()->count(2)->create(['usuario_id' => null]);

    expect(Estudiante::count())->toBe(2);
});

it('impide repetir el expediente de una misma carrera para un estudiante', function () {
    $estudiante = Estudiante::factory()->create();
    $carrera = ['codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '28'];

    Expediente::factory()->for($estudiante)->create($carrera);
    Expediente::factory()->for($estudiante)->create($carrera);
})->throws(QueryException::class);

it('permite un expediente por carrera y el mismo código de carrera en otro estudiante', function () {
    $estudiante = Estudiante::factory()->create();

    Expediente::factory()->for($estudiante)->create(['codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '28']);
    Expediente::factory()->for($estudiante)->create(['codigo_unidad' => '77', 'codigo_extension' => '00', 'codigo_carrera' => '66']);
    Expediente::factory()->create(['codigo_unidad' => '07', 'codigo_extension' => '00', 'codigo_carrera' => '28']);

    expect(Expediente::count())->toBe(3);
});

it('impide repetir el mismo formulario de ingreso en un expediente', function () {
    $formulario = [
        'expediente_id' => Expediente::factory()->create()->id,
        'tipo_formulario' => 'formulario_1',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    DB::table('formularios_ingreso')->insert($formulario);
    DB::table('formularios_ingreso')->insert($formulario);
})->throws(QueryException::class);

it('impide instituciones receptoras duplicadas con el mismo nombre', function () {
    InstitucionReceptora::factory()->create(['nombre' => 'Escuela Oficial Rural Mixta']);

    InstitucionReceptora::factory()->create(['nombre' => 'Escuela Oficial Rural Mixta']);
})->throws(QueryException::class);

it('impide asignar el mismo administrador a dos unidades académicas', function () {
    $administrador = User::factory()->create();

    UnidadAcademica::factory()->create(['administrador_id' => $administrador->id]);
    UnidadAcademica::factory()->create(['administrador_id' => $administrador->id]);
})->throws(QueryException::class);

it('elimina expedientes y registros de los ejes al eliminar definitivamente al estudiante', function () {
    $estudiante = Estudiante::factory()->create();
    $expediente = Expediente::factory()->for($estudiante)->create();
    BienServicio::factory()->for($expediente)->create();

    $estudiante->forceDelete();

    expect(Expediente::count())->toBe(0)
        ->and(BienServicio::withTrashed()->count())->toBe(0);
});

it('conserva la bitácora al eliminar al usuario que la generó', function () {
    $usuario = User::factory()->create();

    DB::table('bitacoras')->insert([
        'usuario_id' => $usuario->id,
        'usuario_nombre' => $usuario->name,
        'modulo' => 'estudiantes',
        'tipo_cambio' => 'crear',
    ]);

    $usuario->delete();

    $registro = DB::table('bitacoras')->first();

    expect($registro)->not->toBeNull()
        ->and($registro->usuario_id)->toBeNull()
        ->and($registro->usuario_nombre)->toBe($usuario->name)
        ->and($registro->fecha_hora)->not->toBeNull();
});
