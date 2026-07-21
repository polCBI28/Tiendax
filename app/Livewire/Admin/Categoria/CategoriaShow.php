<?php

namespace App\Livewire\Admin\Categoria;

use App\Models\Categoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Categoría'])]
class CategoriaShow extends Component
{
    public Categoria $categoria;

    public function mount(Categoria $categoria): void
    {
        $categoria->load([
            'subcategorias' => fn ($q) => $q
                ->withCount('productos')
                ->withCount(['productos as en_stock' => fn ($q) => $q->where('estado', 'en_stock')])
                ->withCount(['productos as bajo_stock' => fn ($q) => $q->whereIn('estado', ['bajo_stock', 'agotado'])]),
        ]);

        $this->categoria = $categoria;
    }

    public function render(): View
    {
        return view('livewire.admin.categoria.categoria-show', [
            'totalProductos' => $this->categoria->subcategorias->sum('productos_count'),
            'enStock' => $this->categoria->subcategorias->sum('en_stock'),
            'conProblemas' => $this->categoria->subcategorias->sum('bajo_stock'),
        ]);
    }
}
