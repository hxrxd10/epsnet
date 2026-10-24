<?php

namespace Database\Factories;

use App\Models\Carrera;
use App\Models\UnidadAcademica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Carrera>
 */
class CarreraFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unidad_academica_id' => UnidadAcademica::factory(),
            'codigo_unidad' => fake()->numerify('##'),
            'codigo_extension' => '00',
            'codigo_carrera' => fake()->unique()->numerify('###'),
            'nombre' => 'Licenciatura en '.fake()->words(2, true),
            'nombre_extension' => 'Plan Diario',
            'nivel_academico' => 'Licenciatura',
        ];
    }
}
