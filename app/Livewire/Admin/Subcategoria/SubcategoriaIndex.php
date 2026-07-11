<?php

namespace App\Livewire\Admin\Subcategoria;

use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Subcategorías'])]
class SubcategoriaIndex extends Component
{
    public function mount(): void
    {
        if (request()->filled('editar')) {
            $this->dispatch('abrir-formulario-subcategoria', subcategoriaId: (int) request('editar'));
        } elseif (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-subcategoria');
        }
    }

    #[On('subcategoria-guardada')]
    #[On('subcategoria-eliminada')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        return view('livewire.admin.subcategoria.subcategoria-index', [
            'totalSubcategorias' => Subcategoria::count(),
            'subcategoriasActivas' => Subcategoria::where('activo', true)->count(),
            'totalCategorias' => Categoria::count(),
        ]);
    }
}
