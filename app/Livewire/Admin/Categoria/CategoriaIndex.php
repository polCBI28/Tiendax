<?php

namespace App\Livewire\Admin\Categoria;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Catálogo de Categorías'])]
class CategoriaIndex extends Component
{
    public function mount(): void
    {
        if (request()->filled('editar')) {
            $this->dispatch('abrir-formulario-categoria', categoriaId: (int) request('editar'));
        } elseif (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-categoria');
        }
    }

    #[On('categoria-guardada')]
    #[On('categoria-eliminada')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        return view('livewire.admin.categoria.categoria-index', [
            'totalCategorias' => Categoria::count(),
            'categoriasActivas' => Categoria::where('activo', true)->count(),
            'totalProductos' => Producto::count(),
            'stockCritico' => Producto::whereColumn('stock', '<=', 'stock_minimo')->count(),
        ]);
    }
}
