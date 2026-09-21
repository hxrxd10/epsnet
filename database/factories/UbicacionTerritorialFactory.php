<?php

namespace Database\Factories;

use App\Models\Expediente;
use App\Models\Municipio;
use App\Models\UbicacionTerritorial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UbicacionTerritorial>
 */
class UbicacionTerritorialFactory extends Factory
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
            'municipio_id' => Municipio::factory(),
            'departamento_id' => fn (array $atributos): int => Municipio::findOrFail($atributos['municipio_id'])->departamento_id,
            'comunidad' => fake()->streetName(),
            'latitud' => fake()->latitude(13.7, 17.8),
            'longitud' => fake()->longitude(-92.2, -88.2),
            'referencia' => null,
        ];
    }
}
