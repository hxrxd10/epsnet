<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Valor base en quetzales de cada bien o servicio del catálogo: con él se calcula el aporte de los EPS.
     */
    public function up(): void
    {
        Schema::table('catalogos', function (Blueprint $table) {
            $table->decimal('valor_base', 12, 2)->nullable()->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('catalogos', function (Blueprint $table) {
            $table->dropColumn('valor_base');
        });
    }
};
