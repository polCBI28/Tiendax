<?php

namespace App\Livewire\Admin\Movimiento;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class MovimientoHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-movimiento');
    }

    public function render(): View
    {
        return view('livewire.admin.movimiento.movimiento-header');
    }
}
