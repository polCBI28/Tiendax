<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stock = fake()->numberBetween(0, 100);
        $stockMinimo = 5;

        return [
            'categoria_id' => Categoria::factory(),
            'nombre' => fake()->unique()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'descripcion' => fake()->sentence(),
            'precio_venta' => fake()->randomFloat(2, 10, 500),
            'precio_costo' => fake()->randomFloat(2, 5, 300),
            'stock' => $stock,
            'stock_minimo' => $stockMinimo,
            'estado' => $stock <= 0 ? 'agotado' : ($stock <= $stockMinimo ? 'bajo_stock' : 'en_stock'),
            'activo' => true,
        ];
    }
}
