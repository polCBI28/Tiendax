<?php

namespace App\Livewire\Admin\DetalleVenta;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DetalleVentaHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-detalle-venta');
    }

    public function render(): View
    {
        return view('livewire.admin.detalle-venta.detalle-venta-header');
    }
}
