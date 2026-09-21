<?php

use App\Http\Controllers\Estudiante\AccesoController;
use App\Http\Controllers\Estudiante\ActorParticipanteController;
use App\Http\Controllers\Estudiante\AlianzaController;
use App\Http\Controllers\Estudiante\BienServicioController;
use App\Http\Controllers\Estudiante\CierreExpedienteController;
use App\Http\Controllers\Estudiante\CuentaController;
use App\Http\Controllers\Estudiante\ExpedienteController;
use App\Http\Controllers\Estudiante\OrdenImpresionController;
use App\Http\Controllers\Estudiante\ProgramaExpedienteController;
use App\Http\Controllers\Estudiante\PublicacionInvestigacionController;
use App\Http\Controllers\Estudiante\SeguimientoImpactoController;
use App\Http\Controllers\Estudiante\SolicitudAprobacionController;
use App\Http\Controllers\Estudiante\TransferenciaConocimientoController;
use App\Http\Controllers\Estudiante\UbicacionTerritorialController;
use Illuminate\Support\Facades\Route;

Route::prefix('estudiante')->name('estudiante.')->group(function () {
    // Verificación de identidad (solo la primera vez): la hacen quienes no tienen cuenta y los invitados.
    Route::get('acceso', [AccesoController::class, 'create'])->name('acceso');
    Route::post('acceso', [AccesoController::class, 'store'])
        ->middleware('throttle:acceso-estudiante')
        ->name('acceso.store');

    // Creación de la cuenta tras verificar la identidad (para quien aún no tiene una).
    Route::middleware('guest')->group(function () {
        Route::get('cuenta', [CuentaController::class, 'create'])->name('cuenta.create');
        Route::post('cuenta', [CuentaController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('cuenta.store');
    });

    Route::middleware(['auth', 'rol:estudiante'])->group(function () {
        Route::get('carreras', [ExpedienteController::class, 'index'])->name('carreras');
        Route::post('expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');

        Route::middleware('can:view,expediente')->group(function () {
            Route::get('expedientes/{expediente}', [ExpedienteController::class, 'show'])->name('expedientes.show');

            $ejes = [
                'bienes-servicios' => BienServicioController::class,
                'publicaciones' => PublicacionInvestigacionController::class,
                'transferencias' => TransferenciaConocimientoController::class,
                'territorio' => UbicacionTerritorialController::class,
                'actores' => ActorParticipanteController::class,
                'seguimiento' => SeguimientoImpactoController::class,
            ];

            foreach ($ejes as $segmento => $controlador) {
                Route::resource("expedientes.{$segmento}", $controlador)
                    ->only(['index', 'store', 'update', 'destroy'])
                    ->parameters([$segmento => 'registro']);
            }

            // Instituciones aliadas del EPS: se muestran en el paso de actores.
            Route::resource('expedientes.alianzas', AlianzaController::class)
                ->only(['store', 'update', 'destroy'])
                ->parameters(['alianzas' => 'registro']);

            Route::post('expedientes/{expediente}/solicitud-aprobacion', [SolicitudAprobacionController::class, 'store'])->name('expedientes.solicitud.store');
            Route::put('expedientes/{expediente}/programa', [ProgramaExpedienteController::class, 'update'])->name('expedientes.programa.update');

            Route::get('expedientes/{expediente}/cierre', [CierreExpedienteController::class, 'index'])->name('expedientes.cierre.index');
            Route::post('expedientes/{expediente}/cierre', [CierreExpedienteController::class, 'store'])->name('expedientes.cierre.store');

            Route::get('expedientes/{expediente}/orden-impresion', [OrdenImpresionController::class, 'show'])->name('expedientes.orden-impresion.show');
            Route::post('expedientes/{expediente}/orden-impresion', [OrdenImpresionController::class, 'store'])->name('expedientes.orden-impresion.store');
            Route::delete('expedientes/{expediente}/orden-impresion', [OrdenImpresionController::class, 'destroy'])->name('expedientes.orden-impresion.destroy');
        });
    });
});
