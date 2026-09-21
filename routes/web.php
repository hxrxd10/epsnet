<?php

use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Admin\CatalogoController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\ManejoDatosController;
use App\Http\Controllers\Admin\MunicipioController;
use App\Http\Controllers\Admin\UnidadAcademicaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\EstadisticaPdfController;
use App\Http\Controllers\Panel\EstudianteController;
use App\Http\Controllers\Panel\OrdenImpresionController as PanelOrdenImpresionController;
use App\Http\Controllers\Panel\VerificacionExpedienteController;
use App\Http\Controllers\RepositorioController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::get('registro', [RegistroController::class, 'create'])->name('registro');
    Route::post('registro', [RegistroController::class, 'store'])->name('registro.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::inertia('documentacion', 'documentacion')->name('documentacion');

    Route::get('estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas.index');
    Route::get('estadisticas/pdf', [EstadisticaPdfController::class, 'index'])->name('estadisticas.pdf');
    Route::get('estadisticas/departamentos/{departamento:codigo}', [EstadisticaController::class, 'departamento'])->name('estadisticas.departamento');
    Route::get('estadisticas/departamentos/{departamento:codigo}/pdf', [EstadisticaPdfController::class, 'departamento'])->name('estadisticas.departamento.pdf');

    Route::get('repositorio', [RepositorioController::class, 'index'])->name('repositorio.index');
    Route::get('repositorio/{expediente}', [RepositorioController::class, 'show'])->name('repositorio.show');
});

// Estudiantes y EPS: DIGEU ve todos y cada unidad académica los de su unidad.
Route::middleware(['auth', 'verified', 'rol:digeu,unidad_academica'])->prefix('estudiantes')->name('panel.estudiantes.')->group(function () {
    Route::get('/', [EstudianteController::class, 'index'])->name('index');

    Route::middleware('can:consultar,expediente')->group(function () {
        Route::get('{expediente}', [EstudianteController::class, 'show'])->name('show');
        Route::get('{expediente}/orden-impresion', [PanelOrdenImpresionController::class, 'show'])->name('orden-impresion');
        Route::post('{expediente}/verificacion', [VerificacionExpedienteController::class, 'store'])->name('verificacion.store');
        Route::delete('{expediente}/verificacion', [VerificacionExpedienteController::class, 'destroy'])->name('verificacion.destroy');
    });
});

// Manejo de datos: solo para administradores (DIGEU).
Route::middleware(['auth', 'verified', 'rol:digeu'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('datos', [ManejoDatosController::class, 'index'])->name('datos');
    Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');

    Route::resource('departamentos', DepartamentoController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('municipios', MunicipioController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('unidades', UnidadAcademicaController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['unidades' => 'unidad']);

    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');

    Route::get('catalogos/{catalogo}', [CatalogoController::class, 'index'])->name('catalogos.index');
    Route::post('catalogos/{catalogo}', [CatalogoController::class, 'store'])->name('catalogos.store');
    Route::put('catalogos/{catalogo}/{item}', [CatalogoController::class, 'update'])->whereNumber('item')->name('catalogos.update');
    Route::delete('catalogos/{catalogo}/{item}', [CatalogoController::class, 'destroy'])->whereNumber('item')->name('catalogos.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/estudiante.php';
