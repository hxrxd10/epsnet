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
        Schema::create('seguimientos_impacto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo_registro', 30)->default('avance')->index();
            $table->string('indicador', 500);
            $table->text('avance')->nullable();
            $table->unsignedTinyInteger('porcentaje_avance')->nullable();
            $table->string('cumplimiento', 30)->nullable();
            $table->text('observaciones')->nullable();
            $table->text('evaluacion_impacto')->nullable();
            $table->date('fecha')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos_impacto');
    }
};
