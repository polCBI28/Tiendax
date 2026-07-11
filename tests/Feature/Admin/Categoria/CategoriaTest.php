<?php

use App\Livewire\Admin\Categoria\CategoriaForm;
use App\Livewire\Admin\Categoria\CategoriaTable;
use App\Models\Categoria;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de categorías carga correctamente', function () {
    $this->get(route('categorias.index'))->assertOk();
});

test('la tabla lista categorías y filtra por búsqueda', function () {
    Categoria::factory()->create(['nombre' => 'Ropa Deportiva']);
    Categoria::factory()->create(['nombre' => 'Electrónica']);

    Livewire::test(CategoriaTable::class)
        ->assertSee('Ropa Deportiva')
        ->assertSee('Electrónica')
        ->set('search', 'Ropa')
        ->assertSee('Ropa Deportiva')
        ->assertDontSee('Electrónica');
});

test('se puede crear una categoría desde el formulario', function () {
    Livewire::test(CategoriaForm::class)
        ->call('abrir')
        ->set('nombre', 'Hogar')
        ->set('descripcion', 'Artículos para el hogar')
        ->call('seleccionarIcono', 'chair')
        ->call('guardar')
        ->assertHasNoErrors()
        ->assertDispatched('categoria-guardada');

    $categoria = Categoria::where('nombre', 'Hogar')->firstOrFail();
    expect($categoria->icono)->toBe('chair');
});

test('el nombre es requerido', function () {
    Livewire::test(CategoriaForm::class)
        ->call('abrir')
        ->set('nombre', '')
        ->call('guardar')
        ->assertHasErrors(['nombre']);
});

test('se puede editar una categoría existente', function () {
    $categoria = Categoria::factory()->create(['nombre' => 'Nombre Viejo']);

    Livewire::test(CategoriaForm::class)
        ->call('abrir', $categoria->id)
        ->assertSet('nombre', 'Nombre Viejo')
        ->set('nombre', 'Nombre Nuevo')
        ->call('guardar')
        ->assertHasNoErrors();

    expect($categoria->fresh()->nombre)->toBe('Nombre Nuevo');
});

test('se puede eliminar una categoría desde la tabla', function () {
    $categoria = Categoria::factory()->create();

    Livewire::test(CategoriaTable::class)
        ->call('eliminar', $categoria->id);

    $this->assertModelMissing($categoria);
});
