<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Una unidad académica deja de tener un solo administrador: cada usuario de unidad académica
     * (p. ej. un supervisor por carrera) indica a qué unidad pertenece. Se conservan las asignaciones.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('unidad_academica_id')->nullable()->after('rol_id')->constrained('unidades_academicas')->nullOnDelete();
        });

        DB::table('unidades_academicas')->whereNotNull('administrador_id')->get(['id', 'administrador_id'])->each(function (object $unidad): void {
            DB::table('users')->where('id', $unidad->administrador_id)->update(['unidad_academica_id' => $unidad->id]);
        });

        Schema::table('unidades_academicas', function (Blueprint $table) {
            $table->dropForeign(['administrador_id']);
            $table->dropUnique(['administrador_id']);
        });

        Schema::table('unidades_academicas', function (Blueprint $table) {
            $table->dropColumn('administrador_id');
        });
    }

    /**
     * Al volver atrás, cada unidad conserva solo a uno de sus usuarios como administrador.
     */
    public function down(): void
    {
        Schema::table('unidades_academicas', function (Blueprint $table) {
            $table->foreignId('administrador_id')->nullable()->unique()->after('direccion')->constrained('users')->nullOnDelete();
        });

        DB::table('users')->whereNotNull('unidad_academica_id')->orderBy('id')->get(['id', 'unidad_academica_id'])
            ->unique('unidad_academica_id')
            ->each(function (object $usuario): void {
                DB::table('unidades_academicas')->where('id', $usuario->unidad_academica_id)->update(['administrador_id' => $usuario->id]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unidad_academica_id');
        });
    }
};
