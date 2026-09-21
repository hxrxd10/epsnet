<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de municipios: cada uno pertenece a un departamento y tiene una coordenada de referencia.
     */
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->restrictOnDelete();
            $table->string('codigo', 4)->unique();
            $table->string('nombre', 150);
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['departamento_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
