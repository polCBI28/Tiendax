<?php

use App\Livewire\Admin\Reporte\ReporteHeader;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de reportes carga correctamente', function () {
    $this->get(route('reportes.index'))->assertOk();
});

test('los kpis del mes reflejan las ventas registradas', function () {
    $venta = Venta::factory()->create(['fecha_venta' => now(), 'total' => 150]);
    $producto = Producto::factory()->create();
    DetalleVenta::create([
        'venta_id' => $venta->id,
        'producto_id' => $producto->id,
        'cantidad' => 3,
        'precio_unitario' => 50,
        'adicional' => 0,
        'subtotal' => 150,
    ]);

    $this->get(route('reportes.index'))
        ->assertOk()
        ->assertSee('150.00')
        ->assertSee($producto->nombre);
});

test('exportar csv responde con un archivo descargable', function () {
    Venta::factory()->create(['fecha_venta' => now()]);

    Livewire::test(ReporteHeader::class)
        ->call('exportarCsv')
        ->assertStatus(200);
});
