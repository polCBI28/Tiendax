<?php

namespace App\Livewire\Admin\DetalleVenta;

use App\Models\DetalleVenta;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Detalle de Ventas'])]
class DetalleVentaIndex extends Component
{
    public function mount(): void
    {
        if (request()->filled('editar')) {
            $this->dispatch('abrir-formulario-detalle-venta', detalleVentaId: (int) request('editar'));
        } elseif (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-detalle-venta');
        }
    }

    #[On('detalle-venta-guardado')]
    #[On('detalle-venta-eliminado')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        return view('livewire.admin.detalle-venta.detalle-venta-index', [
            'totalLineas' => DetalleVenta::count(),
            'unidadesTotales' => DetalleVenta::sum('cantidad'),
            'ingresosTotales' => DetalleVenta::sum('subtotal'),
        ]);
    }
}
