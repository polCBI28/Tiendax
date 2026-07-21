<?php

namespace App\Livewire\Admin\Reporte;

use App\Models\DetalleVenta;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteHeader extends Component
{
    #[Url]
    public string $desde = '';

    #[Url]
    public string $hasta = '';

    public bool $mostrarRangoPdf = false;

    public function mount(): void
    {
        if ($this->desde === '') {
            $this->desde = now()->startOfMonth()->format('Y-m-d');
        }
        if ($this->hasta === '') {
            $this->hasta = now()->format('Y-m-d');
        }
    }

    public function abrirRangoPdf(): void
    {
        $this->mostrarRangoPdf = true;
    }

    public function generarPdf(): StreamedResponse
    {
        $ventas = Venta::whereDate('fecha_venta', '>=', $this->desde)
            ->whereDate('fecha_venta', '<=', $this->hasta)
            ->get();

        $ingresos = $ventas->sum('total');
        $cantidad = $ventas->count();

        $topProductos = DetalleVenta::select('producto_id')
            ->selectRaw('SUM(cantidad) as total_vendido, SUM(subtotal) as total_ingresos')
            ->with('producto')
            ->whereHas('venta', fn ($q) => $q->whereDate('fecha_venta', '>=', $this->desde)->whereDate('fecha_venta', '<=', $this->hasta))
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        $this->mostrarRangoPdf = false;

        $pdf = Pdf::loadView('exports.reporte-pdf', [
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'ingresos' => $ingresos,
            'cantidad' => $cantidad,
            'ticketPromedio' => $cantidad > 0 ? $ingresos / $cantidad : 0,
            'unidades' => DetalleVenta::whereHas('venta', fn ($q) => $q->whereDate('fecha_venta', '>=', $this->desde)->whereDate('fecha_venta', '<=', $this->hasta))->sum('cantidad'),
            'topProductos' => $topProductos,
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'reporte-'.$this->desde.'-a-'.$this->hasta.'.pdf'
        );
    }

    public function exportarCsv(): StreamedResponse
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
            fwrite($file, "\xEF\xBB\xBF");
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

    public function render(): View
    {
        return view('livewire.admin.reporte.reporte-header');
    }
}
