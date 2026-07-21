<?php

use App\Livewire\NotificacionesWidget;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('muestra productos agotados, con bajo stock y ventas pendientes de cobro', function () {
    $agotado = Producto::factory()->create(['nombre' => 'Producto Agotado', 'estado' => 'agotado', 'stock' => 0]);
    $bajoStock = Producto::factory()->create(['nombre' => 'Producto Bajo Stock', 'estado' => 'bajo_stock', 'stock' => 2, 'stock_minimo' => 5]);
    $venta = Venta::factory()->create(['estado' => 'pendiente', 'total' => 100, 'adelanto' => 50]);

    Livewire::test(NotificacionesWidget::class)
        ->assertSee('Producto Agotado')
        ->assertSee('Producto Bajo Stock')
        ->assertSee($venta->numero_boleta)
        ->assertSee('3 alertas activas');
});

test('no muestra alertas cuando todo está en orden', function () {
    Livewire::test(NotificacionesWidget::class)
        ->assertSee('Todo en orden, sin alertas pendientes.');
});
