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
        Schema::create('formularios_ingreso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->string('tipo_formulario', 50);
            $table->string('estado', 30)->default('borrador')->index();
            $table->json('contenido')->nullable();
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_envio')->nullable();
            $table->timestamps();

            $table->unique(['expediente_id', 'tipo_formulario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formularios_ingreso');
    }
};
