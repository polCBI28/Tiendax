<?php

namespace App\Livewire\Admin\Producto;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProductoHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-producto');
    }

    public function importar(): void
    {
        $this->dispatch('abrir-importar-producto');
    }

    public function render(): View
    {
        return view('livewire.admin.producto.producto-header');
    }
}
