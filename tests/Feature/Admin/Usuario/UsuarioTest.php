<?php

use App\Livewire\Admin\Usuario\UsuarioForm;
use App\Livewire\Admin\Usuario\UsuarioTable;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('Super Admin');

    $this->actingAs($this->superAdmin);
});

test('la página de usuarios carga correctamente para un Super Admin', function () {
    $this->get(route('usuarios.index'))->assertOk();
});

test('un usuario sin el rol Super Admin no puede acceder a usuarios', function () {
    $empleado = User::factory()->create();
    $empleado->assignRole('Empleado');

    $this->actingAs($empleado)
        ->get(route('usuarios.index'))
        ->assertForbidden();
});

test('la tabla lista usuarios y filtra por búsqueda', function () {
    User::factory()->create(['name' => 'Carlos Vendedor', 'email' => 'carlos@example.com']);
    User::factory()->create(['name' => 'Ana Cajera', 'email' => 'ana@example.com']);

    Livewire::test(UsuarioTable::class)
        ->assertSee('Carlos Vendedor')
        ->assertSee('Ana Cajera')
        ->set('search', 'Carlos')
        ->assertSee('Carlos Vendedor')
        ->assertDontSee('Ana Cajera');
});

test('crear un usuario nuevo le asigna el rol seleccionado', function () {
    $rolEmpleado = Role::where('name', 'Empleado')->first();

    Livewire::test(UsuarioForm::class)
        ->call('abrir')
        ->set('name', 'Nuevo Empleado')
        ->set('email', 'nuevo.empleado@example.com')
        ->set('password', 'password123')
        ->set('rolId', (string) $rolEmpleado->id)
        ->call('guardar')
        ->assertHasNoErrors();

    $usuario = User::where('email', 'nuevo.empleado@example.com')->first();

    expect($usuario)->not->toBeNull();
    expect($usuario->hasRole('Empleado'))->toBeTrue();
});

test('editar un usuario existente actualiza sus datos sin exigir contraseña', function () {
    $rolEmpleado = Role::where('name', 'Empleado')->first();
    $usuario = User::factory()->create(['name' => 'Nombre Viejo']);
    $usuario->assignRole('Empleado');

    Livewire::test(UsuarioForm::class)
        ->call('abrir', $usuario->id)
        ->set('name', 'Nombre Actualizado')
        ->set('rolId', (string) $rolEmpleado->id)
        ->call('guardar')
        ->assertHasNoErrors();

    expect($usuario->fresh()->name)->toBe('Nombre Actualizado');
});

test('un usuario no puede eliminarse a sí mismo', function () {
    Livewire::test(UsuarioTable::class)
        ->call('eliminar', $this->superAdmin->id)
        ->assertHasErrors('self');

    expect(User::find($this->superAdmin->id))->not->toBeNull();
});
