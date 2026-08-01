<?php

namespace App\Livewire\Admin\Venta;

use App\Models\Venta;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Detalle de Venta'])]
class VentaShow extends Component
{
    public Venta $venta;

    public function mount(Venta $venta): void
    {
        $venta->load('cliente', 'user', 'detalles.producto');

        $this->venta = $venta;
    }

    public function completarPago(): void
    {
        abort_unless(auth()->user()->can('ventas.editar'), 403);

        $this->venta->update([
            'adelanto' => $this->venta->total,
            'estado' => 'completado',
        ]);

        $this->venta->refresh();

        Flux::toast(text: 'Pago completado correctamente.', variant: 'success');
    }

    public function render(): View
    {
        return view('livewire.admin.venta.venta-show');
    }
}
