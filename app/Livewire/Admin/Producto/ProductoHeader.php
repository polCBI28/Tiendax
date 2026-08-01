<?php

namespace App\Livewire\Admin\Producto;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProductoHeader extends Component
{
    public function crear(): void
    {
        abort_unless(auth()->user()->can('productos.crear'), 403);

        $this->dispatch('abrir-formulario-producto');
    }

    public function importar(): void
    {
        abort_unless(auth()->user()->can('productos.crear'), 403);

        $this->dispatch('abrir-importar-producto');
    }

    public function render(): View
    {
        return view('livewire.admin.producto.producto-header');
    }
}
