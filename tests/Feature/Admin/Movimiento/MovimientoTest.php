<?php

use App\Livewire\Admin\Movimiento\MovimientoForm;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de movimientos carga correctamente', function () {
    $this->get(route('movimientos.index'))->assertOk();
});

test('la tabla agrupa las ventas por día', function () {
    Venta::factory()->create(['fecha_venta' => today(), 'total' => 100, 'estado' => 'completado']);
    Venta::factory()->create(['fecha_venta' => today(), 'total' => 50, 'estado' => 'completado']);

    $this->get(route('movimientos.index'))
        ->assertOk()
        ->assertSee('150.00');
});

test('registrar una entrada aumenta el stock del producto', function () {
    $producto = Producto::factory()->create(['stock' => 10, 'stock_minimo' => 5]);

    Livewire::test(MovimientoForm::class)
        ->call('abrir')
        ->set('categoriaId', $producto->categoria_id)
        ->set('productoId', $producto->id)
        ->set('tipo', 'entrada')
        ->set('cantidad', 20)
        ->call('guardar')
        ->assertHasNoErrors();

    expect($producto->fresh()->stock)->toBe(30);
    expect(Movimiento::where('producto_id', $producto->id)->where('tipo', 'entrada')->exists())->toBeTrue();
});

test('registrar una salida reduce el stock y recalcula el estado', function () {
    $producto = Producto::factory()->create(['stock' => 10, 'stock_minimo' => 5, 'estado' => 'en_stock']);

    Livewire::test(MovimientoForm::class)
        ->call('abrir')
        ->set('categoriaId', $producto->categoria_id)
        ->set('productoId', $producto->id)
        ->set('tipo', 'salida')
        ->set('cantidad', 7)
        ->call('guardar')
        ->assertHasNoErrors();

    $producto->refresh();
    expect($producto->stock)->toBe(3);
    expect($producto->estado)->toBe('bajo_stock');
});

test('una salida con stock insuficiente falla con error', function () {
    $producto = Producto::factory()->create(['stock' => 5, 'stock_minimo' => 2]);

    Livewire::test(MovimientoForm::class)
        ->call('abrir')
        ->set('categoriaId', $producto->categoria_id)
        ->set('productoId', $producto->id)
        ->set('tipo', 'salida')
        ->set('cantidad', 10)
        ->call('guardar')
        ->assertHasErrors(['cantidad']);

    expect($producto->fresh()->stock)->toBe(5);
});

test('el producto es requerido', function () {
    Livewire::test(MovimientoForm::class)
        ->call('abrir')
        ->set('cantidad', 5)
        ->call('guardar')
        ->assertHasErrors(['productoId']);
});
