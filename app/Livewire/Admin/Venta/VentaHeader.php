<?php

namespace App\Livewire\Admin\Venta;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class VentaHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-venta');
    }

    public function render(): View
    {
        return view('livewire.admin.venta.venta-header');
    }
}
