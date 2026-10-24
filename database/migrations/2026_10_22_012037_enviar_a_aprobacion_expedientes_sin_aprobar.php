<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Todo EPS necesita ahora la aprobación de supervisión: los que estaban completos sin aprobar (con
     * su orden de impresión) y los que esperaban aprobación sin completarse pasan a la bandeja, en
     * revisión. Conservan su lugar en la cola según la fecha en que se completaron o se enviaron.
     */
    public function up(): void
    {
        DB::table('expedientes')
            ->where('estado_expediente', 'completo')
            ->whereNull('aprobacion_solicitada_at')
            ->update(['aprobacion_solicitada_at' => DB::raw('coalesce(completado_at, updated_at)')]);

        DB::table('expedientes')
            ->where('estado_expediente', 'activo')
            ->whereNotNull('aprobacion_solicitada_at')
            ->update([
                'estado_expediente' => 'completo',
                'completado_at' => DB::raw('aprobacion_solicitada_at'),
            ]);
    }

    /**
     * Los datos se reinterpretan pero no se pierden; no hay nada que deshacer.
     */
    public function down(): void
    {
        //
    }
};
