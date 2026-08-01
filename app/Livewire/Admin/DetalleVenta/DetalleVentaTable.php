<?php

namespace App\Livewire\Admin\DetalleVenta;

use App\Models\DetalleVenta;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DetalleVentaTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function editar(int $detalleVentaId): void
    {
        abort_unless(auth()->user()->can('ventas.editar'), 403);

        $this->dispatch('abrir-formulario-detalle-venta', detalleVentaId: $detalleVentaId);
    }

    public function eliminar(int $detalleVentaId): void
    {
        abort_unless(auth()->user()->can('ventas.eliminar'), 403);

        DetalleVenta::findOrFail($detalleVentaId)->delete();

        Flux::toast(text: 'Línea eliminada correctamente.', variant: 'success');
        $this->resetPage();
        $this->dispatch('detalle-venta-eliminado');
    }

    #[On('detalle-venta-guardado')]
    public function detalleVentaGuardado(): void
    {
        Flux::toast(text: 'Línea guardada correctamente.', variant: 'success');
    }

    public function render(): View
    {
        $query = DetalleVenta::with('venta', 'producto');

        if ($this->search !== '') {
            $search = $this->search;
            $query->whereHas('producto', fn ($q) => $q->where('nombre', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
        }

        return view('livewire.admin.detalle-venta.detalle-venta-table', [
            'detalleVentas' => $query->latest()->paginate(10),
        ]);
    }
}
