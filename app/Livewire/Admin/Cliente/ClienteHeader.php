<?php

namespace App\Livewire\Admin\Cliente;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ClienteHeader extends Component
{
    public function crear(): void
    {
        abort_unless(auth()->user()->can('clientes.crear'), 403);

        $this->dispatch('abrir-formulario-cliente');
    }

    public function importar(): void
    {
        abort_unless(auth()->user()->can('clientes.crear'), 403);

        $this->dispatch('abrir-importar-cliente');
    }

    public function render(): View
    {
        return view('livewire.admin.cliente.cliente-header');
    }
}
