<?php

namespace App\Livewire\Admin\Venta;

use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Ventas'])]
class VentaIndex extends Component
{
    public function mount(): void
    {
        if (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-venta');
        }
    }

    #[On('venta-guardada')]
    #[On('venta-eliminada')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        $ventasMes = Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)->get();

        return view('livewire.admin.venta.venta-index', [
            'ventasHoy' => Venta::whereDate('fecha_venta', today())->where('estado', '!=', 'cancelado')->count(),
            'ingresosHoy' => Venta::whereDate('fecha_venta', today())->where('estado', '!=', 'cancelado')->sum('total'),
            'pendientes' => Venta::where('estado', 'pendiente')->count(),
            'ticketPromedio' => $ventasMes->count() > 0 ? $ventasMes->sum('total') / $ventasMes->count() : 0,
        ]);
    }
}
