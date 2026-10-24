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

    /**
     * Elemento de otro catálogo (línea de intervención, política, área, ODS…), sin categoría.
     */
    public function del(TipoCatalogo $catalogo): static
    {
        return $this->state(fn (array $attributes) => [
            'catalogo' => $catalogo,
            'categoria' => null,
        ]);
    }

    /**
     * Bien o servicio anclado a su línea de intervención, política pública, área de intervención y ODS.
     */
    public function anclado(): static
    {
        return $this->state(fn (array $attributes) => collect(TipoCatalogo::BienesServicios->vinculos())
            ->map(fn (TipoCatalogo $origen): Factory => self::new()->del($origen))
            ->all());
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => ['activo' => false]);
    }
}
