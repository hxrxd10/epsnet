<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Registra en el catálogo de carreras las que ya eligieron los estudiantes al crear su expediente,
     * para que DIGEU pueda asignarlas a los supervisores. No toca las que ya existen.
     */
    public function up(): void
    {
        $existentes = DB::table('carreras')
            ->get(['codigo_unidad', 'codigo_extension', 'codigo_carrera'])
            ->map(fn (object $carrera): string => "{$carrera->codigo_unidad}-{$carrera->codigo_extension}-{$carrera->codigo_carrera}")
            ->flip();

        $nuevas = DB::table('expedientes')
            ->orderBy('id')
            ->get(['unidad_academica_id', 'codigo_unidad', 'codigo_extension', 'codigo_carrera', 'nombre_carrera', 'nombre_extension', 'nivel_academico'])
            ->unique(fn (object $expediente): string => "{$expediente->codigo_unidad}-{$expediente->codigo_extension}-{$expediente->codigo_carrera}")
            ->reject(fn (object $expediente): bool => $existentes->has("{$expediente->codigo_unidad}-{$expediente->codigo_extension}-{$expediente->codigo_carrera}"))
            ->map(fn (object $expediente): array => [
                'unidad_academica_id' => $expediente->unidad_academica_id,
                'codigo_unidad' => $expediente->codigo_unidad,
                'codigo_extension' => $expediente->codigo_extension,
                'codigo_carrera' => $expediente->codigo_carrera,
                'nombre' => $expediente->nombre_carrera,
                'nombre_extension' => $expediente->nombre_extension,
                'nivel_academico' => $expediente->nivel_academico,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values();

        foreach ($nuevas->chunk(200) as $lote) {
            DB::table('carreras')->insert($lote->all());
        }
    }

    /**
     * No se revierte: no se sabe cuáles carreras agregó DIGEU y cuáles esta migración.
     */
    public function down(): void
    {
        //
    }
};
