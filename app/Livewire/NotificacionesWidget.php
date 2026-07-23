<?php

namespace App\Livewire;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NotificacionesWidget extends Component
{
    public function render(): View
    {
        $datos = Cache::remember('notificaciones-widget', 60, function () {
            $agotadosQuery = Producto::where('estado', 'agotado');
            $bajoStockQuery = Producto::where('estado', 'bajo_stock');
            $ventasPendientesQuery = Venta::where('estado', 'pendiente')
                ->whereColumn('adelanto', '<', 'total');

            $totalAgotados = $agotadosQuery->count();
            $totalBajoStock = $bajoStockQuery->count();
            $totalVentasPendientes = $ventasPendientesQuery->count();

            return [
                'agotados' => (clone $agotadosQuery)->orderBy('nombre')->limit(5)->get(),
                'bajoStock' => (clone $bajoStockQuery)->orderBy('nombre')->limit(5)->get(),
                'ventasPendientes' => (clone $ventasPendientesQuery)->with('cliente')->latest('fecha_venta')->limit(5)->get(),
                'totalAgotados' => $totalAgotados,
                'totalBajoStock' => $totalBajoStock,
                'totalVentasPendientes' => $totalVentasPendientes,
                'total' => $totalAgotados + $totalBajoStock + $totalVentasPendientes,
            ];
        });

        return view('livewire.notificaciones-widget', $datos);
    }
}
