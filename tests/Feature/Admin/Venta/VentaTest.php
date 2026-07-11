<?php

use App\Livewire\Admin\Venta\VentaForm;
use App\Livewire\Admin\Venta\VentaTable;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de ventas carga correctamente', function () {
    $this->get(route('ventas.index'))->assertOk();
});

test('agregar un producto al carrito e incrementar cantidad', function () {
    $producto = Producto::factory()->create(['precio_venta' => 50, 'stock' => 10]);

    Livewire::test(VentaForm::class)
        ->call('abrir')
        ->call('agregarProducto', $producto->id)
        ->call('agregarProducto', $producto->id)
        ->assertSet("carrito.{$producto->id}.cantidad", 2);
});

test('no se puede agregar más unidades que el stock disponible', function () {
    $producto = Producto::factory()->create(['precio_venta' => 50, 'stock' => 1]);

    Livewire::test(VentaForm::class)
        ->call('abrir')
        ->call('agregarProducto', $producto->id)
        ->call('agregarProducto', $producto->id)
        ->assertSet("carrito.{$producto->id}.cantidad", 1);
});

test('registrar una venta completa calcula el total correctamente con descuento porcentaje', function () {
    $categoria = Categoria::factory()->create();
    $p1 = Producto::factory()->create(['categoria_id' => $categoria->id, 'precio_venta' => 50, 'stock' => 10, 'stock_minimo' => 3]);
    $p2 = Producto::factory()->create(['categoria_id' => $categoria->id, 'precio_venta' => 80, 'stock' => 5, 'stock_minimo' => 2]);

    Livewire::test(VentaForm::class)
        ->call('abrir')
        ->set('fechaVenta', now()->format('Y-m-d'))
        ->call('agregarProducto', $p1->id)
        ->call('agregarProducto', $p1->id)
        ->call('agregarProducto', $p2->id)
        ->set('descuentoActivo', true)
        ->set('descuentoTipo', 'porcentaje')
        ->set('descuentoValor', 10)
        ->set('adelanto', 50)
        ->call('guardar', 'completado')
        ->assertHasNoErrors()
        ->assertDispatched('venta-guardada');

    $venta = Venta::latest()->first();
    expect((float) $venta->total)->toBe(162.0);
    expect((float) $venta->descuento_valor)->toBe(10.0);
    expect((float) $venta->adelanto)->toBe(50.0);
    expect($venta->estado)->toBe('pendiente');
    expect($venta->detalles)->toHaveCount(2);

    expect($p1->fresh()->stock)->toBe(8);
    expect($p2->fresh()->stock)->toBe(4);
});

test('un recargo tipo monto se aplica sin límite', function () {
    $producto = Producto::factory()->create(['precio_venta' => 100, 'stock' => 10]);

    Livewire::test(VentaForm::class)
        ->call('abrir')
        ->set('fechaVenta', now()->format('Y-m-d'))
        ->call('agregarProducto', $producto->id)
        ->set('recargoActivo', true)
        ->set('recargoTipo', 'monto')
        ->set('recargoValor', 15)
        ->call('guardar', 'completado')
        ->assertHasNoErrors();

    $venta = Venta::latest()->first();
    expect((float) $venta->total)->toBe(115.0);
    expect($venta->estado)->toBe('completado');
});

test('no se puede guardar una venta sin productos en el carrito', function () {
    Livewire::test(VentaForm::class)
        ->call('abrir')
        ->call('guardar', 'completado')
        ->assertHasErrors(['carrito']);

    expect(Venta::count())->toBe(0);
});

test('se puede cambiar el estado de una venta desde la tabla', function () {
    $venta = Venta::factory()->create(['estado' => 'pendiente']);

    Livewire::test(VentaTable::class)
        ->call('cambiarEstado', $venta->id, 'completado');

    expect($venta->fresh()->estado)->toBe('completado');
});

test('completar pago salda la deuda de la venta', function () {
    $venta = Venta::factory()->create(['total' => 200, 'adelanto' => 50, 'estado' => 'pendiente']);

    Livewire::test(VentaTable::class)
        ->call('completarPago', $venta->id);

    $venta->refresh();
    expect((float) $venta->adelanto)->toBe(200.0);
    expect($venta->estado)->toBe('completado');
});

test('se puede eliminar una venta desde la tabla', function () {
    $venta = Venta::factory()->create();

    Livewire::test(VentaTable::class)
        ->call('eliminar', $venta->id);

    $this->assertModelMissing($venta);
});
