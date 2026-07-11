<?php

use App\Livewire\Admin\Cliente\ClienteForm;
use App\Livewire\Admin\Cliente\ClienteTable;
use App\Models\Cliente;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de clientes carga correctamente', function () {
    $this->get(route('clientes.index'))->assertOk();
});

test('la tabla lista clientes y filtra por búsqueda', function () {
    Cliente::factory()->create(['nombre' => 'Juan Pérez']);
    Cliente::factory()->create(['nombre' => 'María Gómez']);

    Livewire::test(ClienteTable::class)
        ->assertSee('Juan Pérez')
        ->assertSee('María Gómez')
        ->set('search', 'Juan')
        ->assertSee('Juan Pérez')
        ->assertDontSee('María Gómez');
});

test('se puede crear un cliente desde el formulario', function () {
    Livewire::test(ClienteForm::class)
        ->call('abrir')
        ->set('nombre', 'Cliente Nuevo')
        ->set('email', 'nuevo@example.com')
        ->call('guardar')
        ->assertHasNoErrors()
        ->assertDispatched('cliente-guardado');

    expect(Cliente::where('nombre', 'Cliente Nuevo')->exists())->toBeTrue();
});

test('el nombre es requerido', function () {
    Livewire::test(ClienteForm::class)
        ->call('abrir')
        ->set('nombre', '')
        ->call('guardar')
        ->assertHasErrors(['nombre']);
});

test('el email debe ser válido', function () {
    Livewire::test(ClienteForm::class)
        ->call('abrir')
        ->set('nombre', 'Cliente X')
        ->set('email', 'no-es-un-email')
        ->call('guardar')
        ->assertHasErrors(['email']);
});

test('se puede editar un cliente existente', function () {
    $cliente = Cliente::factory()->create(['nombre' => 'Nombre Viejo']);

    Livewire::test(ClienteForm::class)
        ->call('abrir', $cliente->id)
        ->assertSet('nombre', 'Nombre Viejo')
        ->set('nombre', 'Nombre Nuevo')
        ->call('guardar')
        ->assertHasNoErrors();

    expect($cliente->fresh()->nombre)->toBe('Nombre Nuevo');
});

test('se puede eliminar un cliente desde la tabla', function () {
    $cliente = Cliente::factory()->create();

    Livewire::test(ClienteTable::class)
        ->call('eliminar', $cliente->id);

    $this->assertModelMissing($cliente);
});
