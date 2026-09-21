<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coordenadas del edificio de la facultad, escuela o centro.
     */
    public function up(): void
    {
        Schema::table('unidades_academicas', function (Blueprint $table) {
            $table->decimal('latitud', 10, 7)->nullable()->after('direccion');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
        });
    }

    public function down(): void
    {
        Schema::table('unidades_academicas', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};
