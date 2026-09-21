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
        Schema::create('unidades_academicas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('siglas', 30)->nullable();
            $table->string('tipo', 30)->index();
            $table->string('nombre_contacto')->nullable();
            $table->string('correo_contacto')->nullable();
            $table->string('telefono_contacto', 30)->nullable();
            $table->string('direccion', 500)->nullable();
            $table->foreignId('administrador_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades_academicas');
    }
};
