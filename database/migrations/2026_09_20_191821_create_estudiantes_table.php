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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->unique()->constrained('users')->restrictOnDelete();
            $table->string('carnet', 20)->unique();
            $table->string('nombre1', 100);
            $table->string('nombre2', 100)->nullable();
            $table->string('nombre3', 100)->nullable();
            $table->string('apellido1', 100);
            $table->string('apellido2', 100)->nullable();
            $table->string('direccion', 500)->nullable();
            $table->string('codigo_nacionalidad', 10)->nullable();
            $table->string('nacionalidad', 100)->nullable();
            $table->json('carreras')->nullable();
            $table->timestamp('ultima_consulta_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
