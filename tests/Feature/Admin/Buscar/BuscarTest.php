<?php

use App\Livewire\Admin\Buscar\BuscarTable;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de búsqueda carga correctamente', function () {
    $this->get(route('buscar'))->assertOk();
});

test('con menos de 2 caracteres pide escribir más', function () {
    Livewire::test(BuscarTable::class)
        ->set('search', 'a')
        ->assertSee('Escribe al menos 2 caracteres');
});

test('busca productos por nombre o sku', function () {
    Producto::factory()->create(['nombre' => 'Zapatilla Roja', 'sku' => 'ZAP-1']);
    Producto::factory()->create(['nombre' => 'Gorra Azul', 'sku' => 'GOR-1']);

    Livewire::test(BuscarTable::class)
        ->set('search', 'Zapatilla')
        ->assertSee('Zapatilla Roja')
        ->assertDontSee('Gorra Azul');
});

test('busca clientes por nombre, documento o email', function () {
    Cliente::factory()->create(['nombre' => 'Juan Pérez', 'documento' => '99999999']);
    Cliente::factory()->create(['nombre' => 'María López']);

    Livewire::test(BuscarTable::class)
        ->set('search', '99999999')
        ->assertSee('Juan Pérez')
        ->assertDontSee('María López');
});

test('busca ventas por número de boleta', function () {
    Venta::factory()->create(['numero_boleta' => 'B001-000123']);

    Livewire::test(BuscarTable::class)
        ->set('search', '000123')
        ->assertSee('B001-000123');
});

test('sin resultados muestra el mensaje correspondiente', function () {
    Livewire::test(BuscarTable::class)
        ->set('search', 'xyznoexiste')
        ->assertSee('Sin resultados');
});
