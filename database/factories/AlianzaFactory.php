<?php

namespace Database\Factories;

use App\Models\Alianza;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alianza>
 */
class AlianzaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expediente_id' => Expediente::factory(),
            'institucion_aliada_id' => InstitucionAliada::factory(),
            'aporte' => fake()->sentence(),
        ];
    }
}
