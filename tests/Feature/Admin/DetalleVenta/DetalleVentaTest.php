<?php

use App\Livewire\Admin\DetalleVenta\DetalleVentaForm;
use App\Livewire\Admin\DetalleVenta\DetalleVentaTable;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de detalle de ventas carga correctamente', function () {
    $this->get(route('detalle-ventas.index'))->assertOk();
});

test('la tabla lista líneas de detalle y filtra por producto', function () {
    $productoA = Producto::factory()->create(['nombre' => 'Camisa Azul']);
    $productoB = Producto::factory()->create(['nombre' => 'Pantalón Negro']);
    DetalleVenta::factory()->create(['producto_id' => $productoA->id]);
    DetalleVenta::factory()->create(['producto_id' => $productoB->id]);

    Livewire::test(DetalleVentaTable::class)
        ->assertSee('Camisa Azul')
        ->assertSee('Pantalón Negro')
        ->set('search', 'Camisa')
        ->assertSee('Camisa Azul')
        ->assertDontSee('Pantalón Negro');
});

test('se puede crear una línea de detalle desde el formulario', function () {
    $venta = Venta::factory()->create();
    $producto = Producto::factory()->create();

    Livewire::test(DetalleVentaForm::class)
        ->call('abrir')
        ->set('ventaId', $venta->id)
        ->set('productoId', $producto->id)
        ->set('cantidad', 3)
        ->set('precioUnitario', 25)
        ->call('guardar')
        ->assertHasNoErrors()
        ->assertDispatched('detalle-venta-guardado');

    $detalle = DetalleVenta::where('venta_id', $venta->id)->firstOrFail();
    expect((float) $detalle->subtotal)->toBe(75.0);
});

test('la venta y el producto son requeridos', function () {
    Livewire::test(DetalleVentaForm::class)
        ->call('abrir')
        ->set('cantidad', 1)
        ->call('guardar')
        ->assertHasErrors(['ventaId', 'productoId']);
});

test('se puede editar una línea de detalle existente', function () {
    $detalle = DetalleVenta::factory()->create(['cantidad' => 1]);

    Livewire::test(DetalleVentaForm::class)
        ->call('abrir', $detalle->id)
        ->assertSet('cantidad', 1)
        ->set('cantidad', 5)
        ->call('guardar')
        ->assertHasNoErrors();

    expect($detalle->fresh()->cantidad)->toBe(5);
});

test('se puede eliminar una línea de detalle desde la tabla', function () {
    $detalle = DetalleVenta::factory()->create();

    Livewire::test(DetalleVentaTable::class)
        ->call('eliminar', $detalle->id);

    $this->assertModelMissing($detalle);
});
