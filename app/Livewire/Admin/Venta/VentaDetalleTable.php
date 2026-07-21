<?php

namespace App\Livewire\Admin\Venta;

use App\Exports\VentasDetalleExport;
use App\Models\Cliente;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VentaDetalleTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $desde = '';

    #[Url]
    public string $hasta = '';

    #[Url(as: 'cliente_id')]
    public string $clienteId = '';

    #[Url]
    public string $estado = '';

    #[Url]
    public string $descuento = '';

    #[Url]
    public string $ordenar = 'fecha_venta';

    #[Url]
    public string $dir = 'desc';

    /** @var array<int, string> */
    protected array $columnasOrdenables = ['fecha_venta', 'total', 'numero_boleta', 'estado', 'created_at'];

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'desde', 'hasta', 'clienteId', 'estado', 'descuento', 'ordenar', 'dir'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'desde', 'hasta', 'clienteId', 'estado', 'descuento']);
        $this->ordenar = 'fecha_venta';
        $this->dir = 'desc';
        $this->resetPage();
    }

    protected function queryFiltrado(): Builder
    {
        $query = Venta::with('cliente', 'user', 'detalles.producto.categoria');

        if ($this->search !== '') {
            $q = $this->search;
            $query->where(function ($w) use ($q) {
                $w->where('numero_boleta', 'like', "%{$q}%")
                    ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%"));
            });
        }

        if ($this->estado !== '') {
            $query->where('estado', $this->estado);
        }

        if ($this->clienteId !== '') {
            $query->where('cliente_id', $this->clienteId);
        }

        if ($this->desde !== '') {
            $query->whereDate('fecha_venta', '>=', $this->desde);
        }

        if ($this->hasta !== '') {
            $query->whereDate('fecha_venta', '<=', $this->hasta);
        }

        if ($this->descuento !== '') {
            if ($this->descuento === 'con') {
                $query->where('descuento_valor', '>', 0);
            } else {
                $query->where(function ($w) {
                    $w->where('descuento_valor', 0)->orWhereNull('descuento_tipo');
                });
            }
        }

        $ordenar = in_array($this->ordenar, $this->columnasOrdenables) ? $this->ordenar : 'fecha_venta';

        return $query->orderBy($ordenar, $this->dir === 'asc' ? 'asc' : 'desc');
    }

    public function exportarExcel(): BinaryFileResponse
    {
        return (new VentasDetalleExport($this->queryFiltrado()))
            ->download('ventas-detalle-'.now()->format('Y-m-d').'.xlsx');
    }

    public function exportarPdf(): StreamedResponse
    {
        $ventas = $this->queryFiltrado()->get();

        $pdf = Pdf::loadView('exports.ventas-detalle-pdf', [
            'ventas' => $ventas,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'ventas-detalle-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function render(): View
    {
        $query = $this->queryFiltrado();

        $ventas = (clone $query)->paginate(20);

        $estadisticas = [
            'total_ventas' => $ventas->total(),
            'ingresos' => (clone $query)->sum('total'),
            'descuentos' => (clone $query)->where('descuento_valor', '>', 0)->sum(
                DB::raw("CASE WHEN descuento_tipo = 'porcentaje' THEN total * descuento_valor / (100 - descuento_valor) ELSE descuento_valor END")
            ),
            'ticket_promedio' => (clone $query)->avg('total') ?? 0,
        ];

        return view('livewire.admin.venta.venta-detalle-table', [
            'ventas' => $ventas,
            'clientes' => Cliente::orderBy('nombre')->get(),
            'estadisticas' => $estadisticas,
        ]);
    }
}
