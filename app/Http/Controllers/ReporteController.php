<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        // KPIs del mes actual
        $ventasMes     = Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)->get();
        $ingresosMes   = $ventasMes->sum('total');
        $cantidadMes   = $ventasMes->count();
        $ticketPromedio = $cantidadMes > 0 ? $ingresosMes / $cantidadMes : 0;
        $unidadesMes   = DetalleVenta::whereHas('venta', fn($q) =>
            $q->whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)
        )->sum('cantidad');

        // Ventas por día (últimos 30 días) para gráfico
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
            $diasLabels[]  = now()->subDays($i)->format('d/m');
            $diasTotales[] = (float) ($rawPorDia[$fecha]->total ?? 0);
        }

        // Top 10 productos más vendidos
        $topProductos = DetalleVenta::select(
            'producto_id',
            DB::raw('SUM(cantidad) as total_vendido'),
            DB::raw('SUM(subtotal) as total_ingresos')
        )->with('producto')
         ->groupBy('producto_id')
         ->orderByDesc('total_vendido')
         ->limit(10)
         ->get();

        // Resumen últimos 6 meses
        $resumenMeses = Venta::select(
            DB::raw('YEAR(fecha_venta) as anio'),
            DB::raw('MONTH(fecha_venta) as mes'),
            DB::raw('SUM(total) as total'),
            DB::raw('COUNT(*) as cantidad')
        )->where('fecha_venta', '>=', now()->subMonths(5)->startOfMonth())
         ->groupBy('anio', 'mes')
         ->orderBy('anio')->orderBy('mes')
         ->get();

        return view('reportes.index', compact(
            'ingresosMes', 'cantidadMes', 'ticketPromedio', 'unidadesMes',
            'diasLabels', 'diasTotales',
            'topProductos', 'resumenMeses'
        ));
    }

    public function exportarCsv()
    {
        $ventas = Venta::with('cliente')
            ->whereMonth('fecha_venta', now()->month)
            ->whereYear('fecha_venta', now()->year)
            ->orderBy('fecha_venta')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ventas-' . now()->format('Y-m') . '.csv"',
        ];

        $callback = function () use ($ventas) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM para Excel
            fputcsv($file, ['N° Boleta', 'Cliente', 'Fecha', 'Total', 'Estado']);
            foreach ($ventas as $v) {
                fputcsv($file, [
                    $v->numero_boleta,
                    $v->cliente?->nombre ?? 'Sin cliente',
                    $v->fecha_venta,
                    number_format($v->total, 2),
                    $v->estado,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
