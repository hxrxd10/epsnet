<?php

namespace Database\Factories;

use App\Enums\NivelCumplimiento;
use App\Enums\TipoRegistroSeguimiento;
use App\Models\Expediente;
use App\Models\SeguimientoImpacto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeguimientoImpacto>
 */
class SeguimientoImpactoFactory extends Factory
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
            'tipo_registro' => TipoRegistroSeguimiento::Avance,
            'indicador' => fake()->sentence(6),
            'avance' => fake()->paragraph(),
            'porcentaje_avance' => fake()->numberBetween(0, 100),
            'cumplimiento' => fake()->randomElement(NivelCumplimiento::cases()),
            'observaciones' => null,
            'evaluacion_impacto' => null,
            'fecha' => fake()->date(),
        ];
    }
}
