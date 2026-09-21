<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Momento en que el estudiante envió su EPS a aprobación de la unidad académica (bandeja de solicitudes).
     * Se limpia cuando el EPS se aprueba.
     */
    public function up(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->timestamp('aprobacion_solicitada_at')->nullable()->after('verificado_por')->index();
        });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropIndex(['aprobacion_solicitada_at']);
            $table->dropColumn('aprobacion_solicitada_at');
        });
    }
};
