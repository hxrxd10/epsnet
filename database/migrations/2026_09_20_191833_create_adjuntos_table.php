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
        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id();
            $table->string('categoria', 50)->nullable()->index();
            $table->string('entidad_tipo');
            $table->unsignedBigInteger('entidad_id');
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre_original');
            $table->string('ruta_archivo', 500)->nullable();
            $table->string('disco', 50)->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->char('sha256', 64)->nullable();
            $table->timestamp('fecha_subida')->useCurrent();

            $table->index(['entidad_tipo', 'entidad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
    }
};
