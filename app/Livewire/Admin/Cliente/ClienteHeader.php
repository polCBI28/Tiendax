<?php

namespace App\Livewire\Admin\Cliente;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ClienteHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-cliente');
    }

    public function render(): View
    {
        return view('livewire.admin.cliente.cliente-header');
    }
}
