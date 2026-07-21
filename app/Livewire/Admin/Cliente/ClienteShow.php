<?php

namespace App\Livewire\Admin\Cliente;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Detalle de Cliente'])]
class ClienteShow extends Component
{
    public Cliente $cliente;

    public function mount(Cliente $cliente): void
    {
        $cliente->load('ventas');

        $this->cliente = $cliente;
    }

    public function render(): View
    {
        return view('livewire.admin.cliente.cliente-show');
    }
}
