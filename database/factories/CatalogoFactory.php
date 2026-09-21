<?php

namespace Database\Factories;

use App\Enums\TipoCatalogo;
use App\Models\Catalogo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Catalogo>
 */
class CatalogoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'catalogo' => TipoCatalogo::BienesServicios,
            'nombre' => fake()->unique()->words(3, true),
            'descripcion' => fake()->sentence(),
            'categoria' => 'servicio',
            'activo' => true,
        ];
    }

    public function deAcciones(): static
    {
        return $this->state(fn (array $attributes) => [
            'catalogo' => TipoCatalogo::Acciones,
            'categoria' => null,
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => ['activo' => false]);
    }
}
