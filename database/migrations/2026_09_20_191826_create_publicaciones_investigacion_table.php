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
        Schema::create('publicaciones_investigacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->text('titulo');
            $table->string('tipo', 50);
            $table->text('autores');
            $table->string('medio_publicacion', 500)->nullable();
            $table->text('resumen')->nullable();
            $table->string('enlace', 2048)->nullable();
            $table->date('fecha_publicacion')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones_investigacion');
    }
};
