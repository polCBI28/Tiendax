<?php

namespace App\Livewire\Admin\DetalleVenta;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DetalleVentaForm extends Component
{
    public bool $mostrarModal = false;

    public ?int $detalleVentaId = null;

    public ?int $ventaId = null;

    public ?int $productoId = null;

    public int $cantidad = 1;

    public float $precioUnitario = 0;

    public float $adicional = 0;

    public function rules(): array
    {
        return [
            'ventaId' => ['required', 'exists:ventas,id'],
            'productoId' => ['required', 'exists:productos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precioUnitario' => ['required', 'numeric', 'min:0'],
            'adicional' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    #[On('abrir-formulario-detalle-venta')]
    public function abrir(?int $detalleVentaId = null): void
    {
        $this->resetValidation();
        $this->detalleVentaId = $detalleVentaId;

        if ($detalleVentaId) {
            $detalle = DetalleVenta::findOrFail($detalleVentaId);
            $this->ventaId = $detalle->venta_id;
            $this->productoId = $detalle->producto_id;
            $this->cantidad = $detalle->cantidad;
            $this->precioUnitario = (float) $detalle->precio_unitario;
            $this->adicional = (float) $detalle->adicional;
        } else {
            $this->reset(['ventaId', 'productoId', 'precioUnitario', 'adicional']);
            $this->cantidad = 1;
        }

        $this->mostrarModal = true;
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
        $this->resetValidation();
    }

    public function guardar(): void
    {
        $validated = $this->validate();

        $data = [
            'venta_id' => $validated['ventaId'],
            'producto_id' => $validated['productoId'],
            'cantidad' => $validated['cantidad'],
            'precio_unitario' => $validated['precioUnitario'],
            'adicional' => $this->adicional,
            'subtotal' => $validated['precioUnitario'] * $validated['cantidad'],
        ];

        if ($this->detalleVentaId) {
            DetalleVenta::findOrFail($this->detalleVentaId)->update($data);
        } else {
            DetalleVenta::create($data);
        }

        $this->mostrarModal = false;
        $this->dispatch('detalle-venta-guardado');
    }

    public function render(): View
    {
        return view('livewire.admin.detalle-venta.detalle-venta-form', [
            'ventas' => Venta::latest()->limit(50)->get(),
            'productos' => Producto::orderBy('nombre')->get(),
        ]);
    }
}
