<?php

namespace App\Livewire\Admin\Venta;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Detalle de Ventas'])]
class VentaDetalleIndex extends Component
{
    public function render(): View
    {
        return view('livewire.admin.venta.venta-detalle-index');
    }
}
