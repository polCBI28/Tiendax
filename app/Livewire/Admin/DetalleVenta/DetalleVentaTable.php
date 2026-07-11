<?php

namespace App\Livewire\Admin\DetalleVenta;

use App\Models\DetalleVenta;
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

    public ?string $mensaje = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function editar(int $detalleVentaId): void
    {
        $this->dispatch('abrir-formulario-detalle-venta', detalleVentaId: $detalleVentaId);
    }

    public function eliminar(int $detalleVentaId): void
    {
        DetalleVenta::findOrFail($detalleVentaId)->delete();

        $this->mensaje = 'Línea eliminada correctamente.';
        $this->resetPage();
        $this->dispatch('detalle-venta-eliminado');
    }

    #[On('detalle-venta-guardado')]
    public function detalleVentaGuardado(): void
    {
        $this->mensaje = 'Línea guardada correctamente.';
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
