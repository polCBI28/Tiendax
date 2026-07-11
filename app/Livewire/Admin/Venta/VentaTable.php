<?php

namespace App\Livewire\Admin\Venta;

use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VentaTable extends Component
{
    use WithPagination;

    #[Url]
    public string $estado = '';

    public ?string $mensaje = null;

    public function updatingEstado(): void
    {
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

    public function render(): View
    {
        $query = Venta::with('cliente', 'user');

        if ($this->estado !== '') {
            $query->where('estado', $this->estado);
        }

        return view('livewire.admin.venta.venta-table', [
            'ventas' => $query->latest('fecha_venta')->paginate(10),
        ]);
    }
}
