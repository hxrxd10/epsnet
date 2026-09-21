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
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('catalogo', 50);
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['catalogo', 'nombre']);
            $table->index(['catalogo', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
