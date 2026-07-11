<?php

use App\Livewire\Admin\Producto\ProductoForm;
use App\Livewire\Admin\Producto\ProductoTable;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de productos carga correctamente', function () {
    $this->get(route('productos.index'))->assertOk();
});

test('la tabla lista productos y filtra por categoría', function () {
    $categoriaA = Categoria::factory()->create();
    $categoriaB = Categoria::factory()->create();

    Producto::factory()->create(['categoria_id' => $categoriaA->id, 'nombre' => 'Camisa Roja']);
    Producto::factory()->create(['categoria_id' => $categoriaB->id, 'nombre' => 'Pantalón Azul']);

    Livewire::test(ProductoTable::class)
        ->assertSee('Camisa Roja')
        ->assertSee('Pantalón Azul')
        ->set('categoriaId', $categoriaA->id)
        ->assertSee('Camisa Roja')
        ->assertDontSee('Pantalón Azul');
});

test('la tabla filtra por búsqueda de nombre o sku', function () {
    Producto::factory()->create(['nombre' => 'Zapatilla Negra', 'sku' => 'ZAP-001']);
    Producto::factory()->create(['nombre' => 'Gorra Blanca', 'sku' => 'GOR-002']);

    Livewire::test(ProductoTable::class)
        ->set('search', 'Zapatilla')
        ->assertSee('Zapatilla Negra')
        ->assertDontSee('Gorra Blanca');
});

test('se puede crear un producto desde el formulario', function () {
    $categoria = Categoria::factory()->create();

    Livewire::test(ProductoForm::class)
        ->call('abrir')
        ->set('nombre', 'Producto Nuevo')
        ->set('sku', 'SKU-NEW-1')
        ->set('categoriaId', $categoria->id)
        ->set('precioVenta', 99.90)
        ->set('stock', 10)
        ->set('stockMinimo', 3)
        ->call('guardar')
        ->assertHasNoErrors()
        ->assertDispatched('producto-guardado');

    $producto = Producto::where('sku', 'SKU-NEW-1')->firstOrFail();
    expect($producto->nombre)->toBe('Producto Nuevo');
    expect($producto->estado)->toBe('en_stock');
});

test('el sku duplicado no pasa la validación', function () {
    $categoria = Categoria::factory()->create();
    Producto::factory()->create(['sku' => 'SKU-DUP-1']);

    Livewire::test(ProductoForm::class)
        ->call('abrir')
        ->set('nombre', 'Otro Producto')
        ->set('sku', 'SKU-DUP-1')
        ->set('categoriaId', $categoria->id)
        ->set('precioVenta', 50)
        ->set('stock', 5)
        ->set('stockMinimo', 2)
        ->call('guardar')
        ->assertHasErrors(['sku']);
});

test('se puede editar un producto existente', function () {
    $producto = Producto::factory()->create(['nombre' => 'Nombre Viejo']);

    Livewire::test(ProductoForm::class)
        ->call('abrir', $producto->id)
        ->assertSet('nombre', 'Nombre Viejo')
        ->set('nombre', 'Nombre Actualizado')
        ->call('guardar')
        ->assertHasNoErrors();

    expect($producto->fresh()->nombre)->toBe('Nombre Actualizado');
});

test('se recalcula el estado al bajar el stock por debajo del mínimo', function () {
    $producto = Producto::factory()->create(['stock' => 20, 'stock_minimo' => 5, 'estado' => 'en_stock']);

    Livewire::test(ProductoForm::class)
        ->call('abrir', $producto->id)
        ->set('stock', 2)
        ->call('guardar')
        ->assertHasNoErrors();

    expect($producto->fresh()->estado)->toBe('bajo_stock');
});

test('se puede eliminar un producto desde la tabla', function () {
    $producto = Producto::factory()->create();

    Livewire::test(ProductoTable::class)
        ->call('eliminar', $producto->id);

    $this->assertModelMissing($producto);
});
