<?php

namespace App\Livewire\Admin\Movimiento;

use App\Models\Movimiento;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Movimientos'])]
class MovimientoIndex extends Component
{
    public function mount(): void
    {
        if (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-movimiento');
        }
    }

    #[On('movimiento-guardado')]
    #[On('movimiento-eliminado')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        $ajustesQuery = fn ($q) => $q->where('motivo', 'not like', 'Venta %')->orWhereNull('motivo');

        return view('livewire.admin.movimiento.movimiento-index', [
            'ventasHoy' => Venta::whereDate('fecha_venta', today())->where('estado', '!=', 'cancelado')->count(),
            'entradasHoy' => Movimiento::whereDate('fecha', today())->where('tipo', 'entrada')->count(),
            'salidasHoy' => Movimiento::whereDate('fecha', today())->where('tipo', 'salida')->count(),
            'ajustesMes' => Movimiento::whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->where($ajustesQuery)->count(),
        ]);
    }
}
