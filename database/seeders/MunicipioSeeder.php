<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    /**
     * Catálogo de los 340 municipios. Requiere los departamentos y es idempotente: no pisa las
     * coordenadas que DIGEU haya afinado.
     */
    public function run(): void
    {
        $departamentos = Departamento::pluck('id', 'codigo');

        /** @var list<array{0: string, 1: string, 2: float, 3: float}> $municipios */
        $municipios = require __DIR__.'/data/municipios.php';

        foreach ($municipios as [$codigo, $nombre, $latitud, $longitud]) {
            $departamentoId = $departamentos[substr($codigo, 0, 2)] ?? null;

            if ($departamentoId === null) {
                continue;
            }

            Municipio::firstOrCreate(
                ['codigo' => $codigo],
                ['departamento_id' => $departamentoId, 'nombre' => $nombre, 'latitud' => $latitud, 'longitud' => $longitud],
            );
        }
    }
}
