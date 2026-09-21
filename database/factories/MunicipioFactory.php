<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Municipio>
 */
class MunicipioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'departamento_id' => Departamento::factory(),
            'codigo' => fake()->unique()->numerify('####'),
            'nombre' => fake()->unique()->city(),
            'latitud' => fake()->latitude(13.7, 17.8),
            'longitud' => fake()->longitude(-92.2, -88.2),
        ];
    }
}
