<?php

namespace Database\Factories;

use App\Models\Departamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Departamento>
 */
class DepartamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => fake()->unique()->numerify('##'),
            'nombre' => fake()->unique()->state(),
            'cabecera' => fake()->city(),
            'latitud' => fake()->latitude(13.7, 17.8),
            'longitud' => fake()->longitude(-92.2, -88.2),
        ];
    }
}
