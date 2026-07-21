<?php

namespace App\Livewire;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NotificacionesWidget extends Component
{
    public function render(): View
    {
        $agotadosQuery = Producto::where('estado', 'agotado');
        $bajoStockQuery = Producto::where('estado', 'bajo_stock');
        $ventasPendientesQuery = Venta::where('estado', 'pendiente')
            ->whereColumn('adelanto', '<', 'total');

        $totalAgotados = $agotadosQuery->count();
        $totalBajoStock = $bajoStockQuery->count();
        $totalVentasPendientes = $ventasPendientesQuery->count();

        return view('livewire.notificaciones-widget', [
            'agotados' => (clone $agotadosQuery)->orderBy('nombre')->limit(5)->get(),
            'bajoStock' => (clone $bajoStockQuery)->orderBy('nombre')->limit(5)->get(),
            'ventasPendientes' => (clone $ventasPendientesQuery)->with('cliente')->latest('fecha_venta')->limit(5)->get(),
            'totalAgotados' => $totalAgotados,
            'totalBajoStock' => $totalBajoStock,
            'totalVentasPendientes' => $totalVentasPendientes,
            'total' => $totalAgotados + $totalBajoStock + $totalVentasPendientes,
        ]);
    }
}
