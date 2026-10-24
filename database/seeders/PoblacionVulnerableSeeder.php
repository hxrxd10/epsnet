<?php

namespace Database\Seeders;

use App\Enums\TipoCatalogo;
use App\Models\Catalogo;
use Illuminate\Database\Seeder;

class PoblacionVulnerableSeeder extends Seeder
{
    /**
     * Grupos en situación de vulnerabilidad que los estudiantes pueden marcar entre los beneficiarios.
     */
    public const array POBLACIONES = [
        ['Mujeres', 'Mujeres, en especial jefas de hogar y mujeres en situación de violencia.'],
        ['Niñez y adolescencia', 'Niñas, niños y adolescentes.'],
        ['Juventud', 'Personas jóvenes.'],
        ['Personas adultas mayores', 'Personas de 60 años o más.'],
        ['Personas con discapacidad', 'Personas con discapacidad física, sensorial, intelectual o psicosocial.'],
        ['Pueblos indígenas', 'Pueblos maya, garífuna y xinka.'],
        ['Población rural', 'Comunidades rurales con acceso limitado a servicios.'],
        ['Población en pobreza o pobreza extrema', 'Hogares en situación de pobreza o pobreza extrema.'],
        ['Personas migrantes y retornadas', 'Personas migrantes, retornadas y sus familias.'],
        ['Víctimas de violencia', 'Personas sobrevivientes de violencia.'],
    ];

    /**
     * Es idempotente: no pisa lo que DIGEU haya cambiado.
     */
    public function run(): void
    {
        foreach (self::POBLACIONES as [$nombre, $descripcion]) {
            Catalogo::firstOrCreate(
                ['catalogo' => TipoCatalogo::PoblacionesVulnerables->value, 'nombre' => $nombre],
                ['descripcion' => $descripcion, 'activo' => true],
            );
        }
    }
}
