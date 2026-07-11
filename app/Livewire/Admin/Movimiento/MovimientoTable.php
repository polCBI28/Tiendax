<?php

namespace App\Livewire\Admin\Movimiento;

use App\Models\DetalleVenta;
use App\Models\Movimiento;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MovimientoTable extends Component
{
    use WithPagination;

    public ?string $mensaje = null;

    #[On('movimiento-guardado')]
    public function movimientoGuardado(): void
    {
        $this->mensaje = 'Movimiento registrado. Stock actualizado.';
    }

    public function render(): View
    {
        $resumenDiario = Venta::select(
            DB::raw('DATE(fecha_venta) as fecha'),
            DB::raw('COUNT(*) as num_ventas'),
            DB::raw('SUM(total) as total_dia')
        )
            ->where('estado', '!=', 'cancelado')
            ->groupBy('fecha')
            ->orderByDesc('fecha')
            ->paginate(10);

        $fechas = $resumenDiario->pluck('fecha')->toArray();

        $unidadesPorDia = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->whereIn(DB::raw('DATE(ventas.fecha_venta)'), $fechas)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                DB::raw('DATE(ventas.fecha_venta) as fecha'),
                DB::raw('SUM(detalle_ventas.cantidad) as unidades')
            )
            ->groupBy('fecha')
            ->pluck('unidades', 'fecha');

        $categoriasPorDia = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->whereIn(DB::raw('DATE(ventas.fecha_venta)'), $fechas)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                DB::raw('DATE(ventas.fecha_venta) as fecha'),
                'categorias.nombre as categoria',
                'categorias.icono',
                DB::raw('SUM(detalle_ventas.subtotal) as total_cat')
            )
            ->groupBy('fecha', 'categorias.nombre', 'categorias.icono')
            ->orderByDesc('total_cat')
            ->get()
            ->groupBy('fecha');

        $movimientosTipoPorDia = Movimiento::whereIn('fecha', $fechas)
            ->select(
                'fecha',
                'tipo',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(cantidad) as unidades')
            )
            ->groupBy('fecha', 'tipo')
            ->get()
            ->groupBy(fn ($m) => $m->fecha->toDateString())
            ->map(fn ($items) => $items->keyBy('tipo'));

        $ajustesPorDia = Movimiento::where(function ($q) {
            $q->where('motivo', 'not like', 'Venta %')->orWhereNull('motivo');
        })
            ->select('fecha', DB::raw('COUNT(*) as total_ajustes'))
            ->groupBy('fecha')
            ->pluck('total_ajustes', 'fecha');

        return view('livewire.admin.movimiento.movimiento-table', compact(
            'resumenDiario', 'unidadesPorDia', 'categoriasPorDia', 'movimientosTipoPorDia', 'ajustesPorDia'
        ));
    }
}
