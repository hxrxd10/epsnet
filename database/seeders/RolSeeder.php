<?php

namespace Database\Seeders;

use App\Enums\ClaveRol;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Roles fijos del sistema: DIGEU, unidad académica y estudiante.
     */
    public function run(): void
    {
        foreach (ClaveRol::cases() as $clave) {
            Rol::updateOrCreate(
                ['clave' => $clave->value],
                ['nombre' => $clave->etiqueta(), 'descripcion' => $clave->descripcion()],
            );
        }
    }
}
