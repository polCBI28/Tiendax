<?php

use App\Livewire\Admin\Subcategoria\SubcategoriaForm;
use App\Livewire\Admin\Subcategoria\SubcategoriaTable;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('la página de subcategorías carga correctamente', function () {
    $this->get(route('subcategorias.index'))->assertOk();
});

test('la tabla lista subcategorías y filtra por categoría', function () {
    $catA = Categoria::factory()->create();
    $catB = Categoria::factory()->create();
    Subcategoria::factory()->create(['categoria_id' => $catA->id, 'nombre' => 'Camisas']);
    Subcategoria::factory()->create(['categoria_id' => $catB->id, 'nombre' => 'Zapatos']);

    Livewire::test(SubcategoriaTable::class)
        ->assertSee('Camisas')
        ->assertSee('Zapatos')
        ->set('categoriaId', $catA->id)
        ->assertSee('Camisas')
        ->assertDontSee('Zapatos');
});

test('se puede crear una subcategoría desde el formulario', function () {
    $categoria = Categoria::factory()->create();

    Livewire::test(SubcategoriaForm::class)
        ->call('abrir')
        ->set('categoriaId', $categoria->id)
        ->set('nombre', 'Accesorios')
        ->call('guardar')
        ->assertHasNoErrors()
        ->assertDispatched('subcategoria-guardada');

    expect(Subcategoria::where('nombre', 'Accesorios')->exists())->toBeTrue();
});

test('la categoría es requerida', function () {
    Livewire::test(SubcategoriaForm::class)
        ->call('abrir')
        ->set('nombre', 'Sin categoría')
        ->call('guardar')
        ->assertHasErrors(['categoriaId']);
});

test('se puede editar una subcategoría existente', function () {
    $sub = Subcategoria::factory()->create(['nombre' => 'Nombre Viejo']);

    Livewire::test(SubcategoriaForm::class)
        ->call('abrir', $sub->id)
        ->assertSet('nombre', 'Nombre Viejo')
        ->set('nombre', 'Nombre Nuevo')
        ->call('guardar')
        ->assertHasNoErrors();

    expect($sub->fresh()->nombre)->toBe('Nombre Nuevo');
});

test('se puede eliminar una subcategoría desde la tabla', function () {
    $sub = Subcategoria::factory()->create();

    Livewire::test(SubcategoriaTable::class)
        ->call('eliminar', $sub->id);

    $this->assertModelMissing($sub);
});
