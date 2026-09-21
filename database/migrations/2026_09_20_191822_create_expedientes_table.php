<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('unidad_academica_id')->constrained('unidades_academicas')->restrictOnDelete();
            $table->string('programa', 30)->nullable();
            $table->string('codigo_unidad', 10);
            $table->string('codigo_extension', 10);
            $table->string('codigo_carrera', 10);
            $table->string('nombre_unidad');
            $table->string('nombre_extension')->nullable();
            $table->string('nombre_carrera', 500);
            $table->string('nivel_academico', 100)->nullable();
            $table->unsignedTinyInteger('eje_actual')->default(1);
            $table->string('estado_expediente', 30)->default('activo')->index();
            $table->timestamp('completado_at')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->foreignId('verificado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_inicio_eps')->nullable();
            $table->date('fecha_fin_eps')->nullable();
            $table->json('datos_epsum')->nullable();
            $table->timestamps();

            $table->unique(['estudiante_id', 'codigo_unidad', 'codigo_extension', 'codigo_carrera'], 'expedientes_estudiante_carrera_unique');
            $table->index(['unidad_academica_id', 'programa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
