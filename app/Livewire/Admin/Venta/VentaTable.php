<?php

namespace App\Livewire\Admin\Venta;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VentaTable extends Component
{
    use WithPagination;

    #[Url]
    public string $estado = '';

    #[Url]
    public string $desde = '';

    #[Url]
    public string $hasta = '';

    public ?string $mensaje = null;

    public function updating(string $property): void
    {
        if (in_array($property, ['estado', 'desde', 'hasta'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['estado', 'desde', 'hasta']);
        $this->resetPage();
    }

    public function cambiarEstado(int $ventaId, string $nuevoEstado): void
    {
        if (! in_array($nuevoEstado, ['borrador', 'pendiente', 'completado', 'cancelado'])) {
            return;
        }

        Venta::findOrFail($ventaId)->update(['estado' => $nuevoEstado]);

        $this->mensaje = 'Estado de la venta actualizado.';
    }

    public function completarPago(int $ventaId): void
    {
        $venta = Venta::findOrFail($ventaId);
        $venta->update(['adelanto' => $venta->total, 'estado' => 'completado']);

        $this->mensaje = 'Pago completado correctamente.';
    }

    public function eliminar(int $ventaId): void
    {
        Venta::findOrFail($ventaId)->delete();

        $this->mensaje = 'Venta eliminada correctamente.';
        $this->resetPage();
        $this->dispatch('venta-eliminada');
    }

    #[On('venta-guardada')]
    public function ventaGuardada(): void
    {
        $this->mensaje = 'Venta registrada correctamente.';
    }

    protected function filteredQuery(): Builder
    {
        $query = Venta::with('cliente', 'user');

        if ($this->estado !== '') {
            $query->where('estado', $this->estado);
        }
        if ($this->desde !== '') {
            $query->whereDate('fecha_venta', '>=', $this->desde);
        }
        if ($this->hasta !== '') {
            $query->whereDate('fecha_venta', '<=', $this->hasta);
        }

        return $query->latest('fecha_venta');
    }

    public function exportarPdf(): StreamedResponse
    {
        $ventas = $this->filteredQuery()->get();

        $pdf = Pdf::loadView('exports.ventas-pdf', [
            'ventas' => $ventas,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'ventas-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function render(): View
    {
        return view('livewire.admin.venta.venta-table', [
            'ventas' => $this->filteredQuery()->paginate(10),
        ]);
    }
}
