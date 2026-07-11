<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venta>
 */
class VentaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'numero_boleta' => 'B001-'.fake()->unique()->numerify('######'),
            'fecha_venta' => now(),
            'total' => fake()->randomFloat(2, 10, 500),
            'adelanto' => 0,
            'estado' => 'completado',
        ];
    }
}
