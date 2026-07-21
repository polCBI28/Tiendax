<?php

use App\Livewire\Admin\Rol\RolForm;
use App\Livewire\Admin\Rol\RolTable;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'productos.ver', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'productos.crear', 'guard_name' => 'web']);

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('Super Admin');

    $this->actingAs($this->superAdmin);
});

test('la página de roles carga correctamente para un Super Admin', function () {
    $this->get(route('roles.index'))->assertOk();
});

test('un usuario sin el rol Super Admin no puede acceder a roles', function () {
    $empleado = User::factory()->create();
    $empleado->assignRole('Empleado');

    $this->actingAs($empleado)
        ->get(route('roles.index'))
        ->assertForbidden();
});

test('la tabla lista roles y filtra por búsqueda', function () {
    Livewire::test(RolTable::class)
        ->assertSee('Super Admin')
        ->assertSee('Empleado')
        ->set('search', 'Super')
        ->assertSee('Super Admin')
        ->assertDontSee('Empleado');
});

test('crear un rol nuevo con permisos existentes', function () {
    $permisoVer = Permission::where('name', 'productos.ver')->first();

    Livewire::test(RolForm::class)
        ->call('abrir')
        ->set('nombre', 'Cajero')
        ->set('permisosSeleccionados', [(string) $permisoVer->id])
        ->call('guardar')
        ->assertHasNoErrors();

    $rol = Role::where('name', 'Cajero')->first();

    expect($rol)->not->toBeNull();
    expect($rol->hasPermissionTo('productos.ver'))->toBeTrue();
});

test('agregar un permiso nuevo desde el formulario lo suma al catálogo y al rol', function () {
    Livewire::test(RolForm::class)
        ->call('abrir')
        ->set('nombre', 'Supervisor')
        ->set('nuevoPermiso', 'inventario.exportar')
        ->call('agregarPermiso')
        ->call('guardar')
        ->assertHasNoErrors();

    expect(Permission::where('name', 'inventario.exportar')->exists())->toBeTrue();

    $rol = Role::where('name', 'Supervisor')->first();
    expect($rol->hasPermissionTo('inventario.exportar'))->toBeTrue();
});

test('editar un rol existente sincroniza sus permisos', function () {
    $rol = Role::create(['name' => 'Rol Editable', 'guard_name' => 'web']);
    $permisoVer = Permission::where('name', 'productos.ver')->first();
    $permisoCrear = Permission::where('name', 'productos.crear')->first();
    $rol->syncPermissions([$permisoVer]);

    Livewire::test(RolForm::class)
        ->call('abrir', $rol->id)
        ->set('permisosSeleccionados', [(string) $permisoCrear->id])
        ->call('guardar')
        ->assertHasNoErrors();

    $rol->refresh();
    expect($rol->hasPermissionTo('productos.crear'))->toBeTrue();
    expect($rol->hasPermissionTo('productos.ver'))->toBeFalse();
});

test('no se puede eliminar el rol Super Admin', function () {
    $superAdminRol = Role::where('name', 'Super Admin')->first();

    Livewire::test(RolTable::class)
        ->call('eliminar', $superAdminRol->id)
        ->assertHasErrors('protegido');

    expect(Role::where('name', 'Super Admin')->exists())->toBeTrue();
});
