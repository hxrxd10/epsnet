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
        Schema::create('adjunto_contenidos', function (Blueprint $table) {
            $table->foreignId('adjunto_id')->primary()->constrained('adjuntos')->cascadeOnDelete();
            $table->longText('contenido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjunto_contenidos');
    }
};
