<?php

namespace App\Livewire\Admin\Producto;

use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Detalle de Productos'])]
class ProductoIndex extends Component
{
    public function mount(): void
    {
        if (request()->filled('editar')) {
            $this->dispatch('abrir-formulario-producto', productoId: (int) request('editar'));
        } elseif (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-producto');
        }
    }

    #[On('producto-guardado')]
    #[On('producto-eliminado')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        return view('livewire.admin.producto.producto-index', [
            'totalProductos' => Producto::count(),
            'bajoStock' => Producto::where('estado', 'bajo_stock')->count(),
            'agotados' => Producto::where('estado', 'agotado')->count(),
            'valorTotal' => Producto::sum(DB::raw('precio_venta * stock')),
            'nuevosEsteMes' => Producto::whereMonth('created_at', now()->month)->count(),
        ]);
    }
}
