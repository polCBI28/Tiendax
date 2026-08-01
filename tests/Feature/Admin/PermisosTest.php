<?php

use App\Livewire\Admin\Producto\ProductoForm;
use App\Livewire\Admin\Producto\ProductoHeader;
use App\Livewire\Admin\Producto\ProductoTable;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('un usuario sin ningún permiso recibe 403 al entrar a productos', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->get(route('productos.index'))
        ->assertForbidden();
});

test('un usuario con solo productos.ver puede ver la página pero no crear, editar ni eliminar', function () {
    $rol = Role::create(['name' => 'Solo Ver Productos', 'guard_name' => 'web']);
    $rol->givePermissionTo('productos.ver');

    $usuario = User::factory()->create();
    $usuario->assignRole($rol);
    $this->actingAs($usuario);

    $this->get(route('productos.index'))->assertOk();

    Livewire::test(ProductoHeader::class)
        ->call('crear')
        ->assertStatus(403);

    $producto = Producto::factory()->create();

    Livewire::test(ProductoTable::class)
        ->call('editar', $producto->id)
        ->assertStatus(403);

    Livewire::test(ProductoTable::class)
        ->call('eliminar', $producto->id)
        ->assertStatus(403);
});

test('un usuario con productos.crear puede guardar un producto nuevo', function () {
    $rol = Role::create(['name' => 'Puede Crear Productos', 'guard_name' => 'web']);
    $rol->givePermissionTo(['productos.ver', 'productos.crear']);

    $usuario = User::factory()->create();
    $usuario->assignRole($rol);
    $this->actingAs($usuario);

    $categoria = Categoria::factory()->create();

    Livewire::test(ProductoForm::class)
        ->call('abrir')
        ->set('nombre', 'Producto de Prueba')
        ->set('sku', 'SKU-PERM-1')
        ->set('categoriaId', $categoria->id)
        ->set('precioVenta', 20)
        ->set('stock', 5)
        ->set('stockMinimo', 2)
        ->call('guardar')
        ->assertHasNoErrors();

    expect(Producto::where('sku', 'SKU-PERM-1')->exists())->toBeTrue();
});

test('el Super Admin no está restringido por ningún permiso', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('Super Admin');
    $this->actingAs($usuario);

    $this->get(route('productos.index'))->assertOk();

    $producto = Producto::factory()->create();

    Livewire::test(ProductoTable::class)
        ->call('editar', $producto->id)
        ->assertStatus(200);
});
