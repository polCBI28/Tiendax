<?php

namespace App\Livewire\Admin\Movimiento;

use App\Models\DetalleVenta;
use App\Models\Movimiento;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Detalle del día'])]
class MovimientoShow extends Component
{
    public string $fecha;

    public function mount(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function render(): View
    {
        $fecha = $this->fecha;
        $fechaCarbon = Carbon::parse($fecha);

        $porCategoria = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->whereDate('ventas.fecha_venta', $fecha)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                'categorias.id as categoria_id',
                'categorias.nombre as categoria',
                'categorias.icono',
                DB::raw('COUNT(DISTINCT ventas.id) as num_ventas'),
                DB::raw('SUM(detalle_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalle_ventas.subtotal) as total')
            )
            ->groupBy('categorias.id', 'categorias.nombre', 'categorias.icono')
            ->orderByDesc('total')
            ->get();

        $totalDia = $porCategoria->sum('total');

        $productosPorCategoria = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->whereDate('ventas.fecha_venta', $fecha)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                'productos.categoria_id',
                'productos.id as producto_id',
                'productos.nombre as producto',
                'productos.sku',
                'productos.imagen',
                DB::raw('SUM(detalle_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalle_ventas.subtotal) as total')
            )
            ->groupBy('productos.categoria_id', 'productos.id', 'productos.nombre', 'productos.sku', 'productos.imagen')
            ->orderByDesc('total')
            ->get()
            ->groupBy('categoria_id');

        $ventasDelDia = Venta::with('cliente')
            ->whereDate('fecha_venta', $fecha)
            ->where('estado', '!=', 'cancelado')
            ->orderByDesc('created_at')
            ->get();

        $ajustes = Movimiento::with('producto', 'user')
            ->whereDate('fecha', $fecha)
            ->where(function ($q) {
                $q->where('motivo', 'not like', 'Venta %')->orWhereNull('motivo');
            })
            ->get();

        return view('livewire.admin.movimiento.movimiento-show', compact(
            'fechaCarbon', 'fecha', 'porCategoria', 'totalDia',
            'productosPorCategoria', 'ventasDelDia', 'ajustes'
        ));
    }
}
