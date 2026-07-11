<?php

namespace App\Livewire\Admin\Cliente;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Clientes'])]
class ClienteIndex extends Component
{
    public function mount(): void
    {
        if (request()->filled('editar')) {
            $this->dispatch('abrir-formulario-cliente', clienteId: (int) request('editar'));
        } elseif (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-cliente');
        }
    }

    #[On('cliente-guardado')]
    #[On('cliente-eliminado')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        return view('livewire.admin.cliente.cliente-index', [
            'totalClientes' => Cliente::count(),
            'clientesEsteMes' => Cliente::whereMonth('created_at', now()->month)->count(),
            'conCompras' => Cliente::has('ventas')->count(),
            'conDocumento' => Cliente::whereNotNull('documento')->count(),
        ]);
    }
}
