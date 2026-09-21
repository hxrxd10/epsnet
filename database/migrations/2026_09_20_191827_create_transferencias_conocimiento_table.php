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
        Schema::create('transferencias_conocimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->foreignId('catalogo_id')->nullable()->constrained('catalogos')->restrictOnDelete();
            $table->string('tipo_actividad', 50);
            $table->text('actividad');
            $table->string('comunidad');
            $table->unsignedInteger('numero_participantes')->nullable();
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
        Schema::dropIfExists('transferencias_conocimiento');
    }
};
