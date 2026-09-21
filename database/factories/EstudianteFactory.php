<?php

namespace Database\Factories;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estudiante>
 */
class EstudianteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'carnet' => (string) fake()->unique()->numerify('20#######'),
            'nombre1' => fake()->firstName(),
            'nombre2' => null,
            'nombre3' => null,
            'apellido1' => fake()->lastName(),
            'apellido2' => fake()->lastName(),
            'direccion' => null,
            'codigo_nacionalidad' => '30',
            'nacionalidad' => 'Guatemalteca',
            'ultima_consulta_at' => now(),
        ];
    }
}
