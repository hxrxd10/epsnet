<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Poblaciones vulnerables (elementos del catálogo) que incluyó cada bien o servicio entre sus beneficiarios.
     */
    public function up(): void
    {
        Schema::create('bien_servicio_poblacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_servicio_id')->constrained('bienes_servicios')->cascadeOnDelete();
            $table->foreignId('catalogo_id')->constrained('catalogos')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['bien_servicio_id', 'catalogo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bien_servicio_poblacion');
    }
};
