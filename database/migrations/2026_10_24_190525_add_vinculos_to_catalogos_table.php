<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cada bien o servicio del catálogo se ancla a una línea de intervención, una política pública, un
     * área de intervención y un ODS, que son también elementos de la tabla `catalogos`. El nombre se
     * amplía porque algunas políticas públicas reúnen varias políticas en un solo texto.
     */
    public function up(): void
    {
        Schema::table('catalogos', function (Blueprint $table) {
            $table->string('nombre', 500)->change();
            $table->foreignId('linea_intervencion_id')->nullable()->after('valor_base')->constrained('catalogos')->restrictOnDelete();
            $table->foreignId('politica_publica_id')->nullable()->after('linea_intervencion_id')->constrained('catalogos')->restrictOnDelete();
            $table->foreignId('area_intervencion_id')->nullable()->after('politica_publica_id')->constrained('catalogos')->restrictOnDelete();
            $table->foreignId('ods_id')->nullable()->after('area_intervencion_id')->constrained('catalogos')->restrictOnDelete();
        });
    }

    /**
     * Los nombres de más de 255 caracteres impedirían volver atrás: hay que acortarlos antes.
     */
    public function down(): void
    {
        Schema::table('catalogos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ods_id');
            $table->dropConstrainedForeignId('area_intervencion_id');
            $table->dropConstrainedForeignId('politica_publica_id');
            $table->dropConstrainedForeignId('linea_intervencion_id');
        });

        Schema::table('catalogos', function (Blueprint $table) {
            $table->string('nombre')->change();
        });
    }
};
