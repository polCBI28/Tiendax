<?php

namespace App\Livewire\Admin\Reporte;

use App\Models\DetalleVenta;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReporteTable extends Component
{
    public function render(): View
    {
        $rawPorDia = Venta::select(
            DB::raw('DATE(fecha_venta) as fecha'),
            DB::raw('SUM(total) as total'),
            DB::raw('COUNT(*) as cantidad')
        )->where('fecha_venta', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->keyBy('fecha');

        $diasLabels = [];
        $diasTotales = [];
        for ($i = 29; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $diasLabels[] = now()->subDays($i)->format('d/m');
            $diasTotales[] = (float) ($rawPorDia[$fecha]->total ?? 0);
        }

        $topProductos = DetalleVenta::select(
            'producto_id',
            DB::raw('SUM(cantidad) as total_vendido'),
            DB::raw('SUM(subtotal) as total_ingresos')
        )->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        $resumenMeses = Venta::where('fecha_venta', '>=', now()->subMonths(5)->startOfMonth())
            ->get()
            ->groupBy(fn ($venta) => Carbon::parse($venta->fecha_venta)->format('Y-m'))
            ->map(function ($ventasDelMes, $clave) {
                [$anio, $mes] = explode('-', $clave);

                return (object) [
                    'anio' => (int) $anio,
                    'mes' => (int) $mes,
                    'total' => $ventasDelMes->sum('total'),
                    'cantidad' => $ventasDelMes->count(),
                ];
            })
            ->sortBy(fn ($item) => $item->anio * 100 + $item->mes)
            ->values();

        return view('livewire.admin.reporte.reporte-table', compact(
            'diasLabels', 'diasTotales', 'topProductos', 'resumenMeses'
        ));
    }
}
