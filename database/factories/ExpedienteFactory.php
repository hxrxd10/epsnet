<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expediente>
 */
class ExpedienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'estudiante_id' => Estudiante::factory(),
            'unidad_academica_id' => UnidadAcademica::factory(),
            'programa' => null,
            'codigo_unidad' => fake()->numerify('##'),
            'codigo_extension' => '00',
            'codigo_carrera' => fake()->numerify('##'),
            'nombre_unidad' => 'Facultad de Humanidades',
            'nombre_extension' => 'Plan Diario',
            'nombre_carrera' => 'Licenciatura en Pedagogía y Administración Educativa',
            'nivel_academico' => 'Licenciatura',
        ];
    }
}
