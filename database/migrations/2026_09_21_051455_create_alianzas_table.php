<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Qué instituciones aliadas tuvo el proyecto de cada EPS y en qué consistió su aporte.
     */
    public function up(): void
    {
        Schema::create('alianzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->foreignId('institucion_aliada_id')->constrained('instituciones_aliadas')->restrictOnDelete();
            $table->text('aporte')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['expediente_id', 'institucion_aliada_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alianzas');
    }
};
