<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Carreras que puede ver y aprobar un supervisor de unidad académica. Sin carreras asignadas, ve
     * todas las de su unidad.
     */
    public function up(): void
    {
        Schema::create('carrera_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carreras')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'carrera_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrera_user');
    }
};
