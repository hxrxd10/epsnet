<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cuántas unidades del bien o servicio se entregaron (p. ej. 40 pupitres). Sin cantidad cuenta como una.
     */
    public function up(): void
    {
        Schema::table('bienes_servicios', function (Blueprint $table) {
            $table->unsignedInteger('cantidad')->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('bienes_servicios', function (Blueprint $table) {
            $table->dropColumn('cantidad');
        });
    }
};
