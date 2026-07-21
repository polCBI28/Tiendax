<?php

namespace App\Livewire\Admin\Producto;

use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Historial de Producto'])]
class ProductoShow extends Component
{
    public Producto $producto;

    public function mount(Producto $producto): void
    {
        $producto->load('categoria', 'subcategoria', 'movimientos');

        $this->producto = $producto;
    }

    public function render(): View
    {
        return view('livewire.admin.producto.producto-show');
    }
}
