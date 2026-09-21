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
        Schema::create('bienes_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->foreignId('catalogo_id')->nullable()->constrained('catalogos')->restrictOnDelete();
            $table->string('tipo', 50);
            $table->text('descripcion');
            $table->text('beneficiarios')->nullable();
            $table->unsignedInteger('cantidad_beneficiarios')->nullable();
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
        Schema::dropIfExists('bienes_servicios');
    }
};
