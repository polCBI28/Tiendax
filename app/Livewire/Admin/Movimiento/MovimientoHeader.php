<?php

namespace App\Livewire\Admin\Movimiento;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class MovimientoHeader extends Component
{
    public function crear(): void
    {
        abort_unless(auth()->user()->can('movimientos.crear'), 403);

        $this->dispatch('abrir-formulario-movimiento');
    }

    public function render(): View
    {
        return view('livewire.admin.movimiento.movimiento-header');
    }
}
