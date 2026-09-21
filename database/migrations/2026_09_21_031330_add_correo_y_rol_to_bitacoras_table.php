<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La bitácora guarda quién hizo cada cambio: además del nombre, su correo y su rol en ese momento.
     */
    public function up(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $table->string('usuario_correo')->nullable()->after('usuario_nombre')->index();
            $table->string('usuario_rol', 50)->nullable()->after('usuario_correo');
        });
    }

    public function down(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $table->dropIndex(['usuario_correo']);
            $table->dropColumn(['usuario_correo', 'usuario_rol']);
        });
    }
};
