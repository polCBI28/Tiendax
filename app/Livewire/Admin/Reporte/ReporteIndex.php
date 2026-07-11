<?php

namespace App\Livewire\Admin\Reporte;

use App\Models\DetalleVenta;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Reportes'])]
class ReporteIndex extends Component
{
    public function render(): View
    {
        $ventasMes = Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)->get();
        $ingresosMes = $ventasMes->sum('total');
        $cantidadMes = $ventasMes->count();

        return view('livewire.admin.reporte.reporte-index', [
            'ingresosMes' => $ingresosMes,
            'cantidadMes' => $cantidadMes,
            'ticketPromedio' => $cantidadMes > 0 ? $ingresosMes / $cantidadMes : 0,
            'unidadesMes' => DetalleVenta::whereHas('venta', fn ($q) => $q->whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year))->sum('cantidad'),
        ]);
    }
}
