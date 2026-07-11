<?php

namespace App\Http\Controllers;

use App\Models\Venta;

class ReporteController extends Controller
{
    public function exportarCsv()
    {
        $ventas = Venta::with('cliente')
            ->whereMonth('fecha_venta', now()->month)
            ->whereYear('fecha_venta', now()->year)
            ->orderBy('fecha_venta')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ventas-'.now()->format('Y-m').'.csv"',
        ];

        $callback = function () use ($ventas) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM para Excel
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
