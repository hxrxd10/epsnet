<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Carreras que DIGEU registra a partir del servicio web de Registro y Estadística. Se identifican
     * como en el servicio (unidad, extensión y carrera), igual que los expedientes.
     */
    public function up(): void
    {
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_academica_id')->constrained('unidades_academicas')->restrictOnDelete();
            $table->string('codigo_unidad', 10);
            $table->string('codigo_extension', 10);
            $table->string('codigo_carrera', 10);
            $table->string('nombre', 500);
            $table->string('nombre_extension')->nullable();
            $table->string('nivel_academico', 100)->nullable();
            $table->timestamps();

            $table->unique(['codigo_unidad', 'codigo_extension', 'codigo_carrera'], 'carreras_codigos_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
