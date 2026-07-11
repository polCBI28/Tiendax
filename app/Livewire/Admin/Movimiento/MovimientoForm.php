<?php

namespace App\Livewire\Admin\Movimiento;

use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class MovimientoForm extends Component
{
    public bool $mostrarModal = false;

    public ?int $categoriaId = null;

    public ?int $subcategoriaId = null;

    public ?int $productoId = null;

    public string $tipo = 'entrada';

    public int $cantidad = 1;

    public string $motivo = '';

    public string $fecha = '';

    public function rules(): array
    {
        return [
            'productoId' => ['required', 'exists:productos,id'],
            'tipo' => ['required', 'in:entrada,salida'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['nullable', 'string', 'max:255'],
            'fecha' => ['required', 'date'],
        ];
    }

    public function updatedCategoriaId(): void
    {
        $this->subcategoriaId = null;
        $this->productoId = null;
    }

    public function updatedSubcategoriaId(): void
    {
        $this->productoId = null;
    }

    #[Computed]
    public function subcategoriasDisponibles()
    {
        if (! $this->categoriaId) {
            return collect();
        }

        return Categoria::find($this->categoriaId)?->subcategorias ?? collect();
    }

    #[Computed]
    public function productosDisponibles()
    {
        $query = Producto::where('activo', true);

        if ($this->subcategoriaId) {
            $query->where('subcategoria_id', $this->subcategoriaId);
        } elseif ($this->categoriaId) {
            $query->where('categoria_id', $this->categoriaId);
        } else {
            return collect();
        }

        return $query->orderBy('nombre')->get();
    }

    #[Computed]
    public function productoSeleccionado(): ?Producto
    {
        return $this->productoId ? Producto::find($this->productoId) : null;
    }

    #[On('abrir-formulario-movimiento')]
    public function abrir(): void
    {
        $this->resetValidation();
        $this->reset(['categoriaId', 'subcategoriaId', 'productoId', 'motivo']);
        $this->tipo = 'entrada';
        $this->cantidad = 1;
        $this->fecha = now()->format('Y-m-d');
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

        $producto = Producto::findOrFail($validated['productoId']);

        if ($validated['tipo'] === 'salida' && $producto->stock < $validated['cantidad']) {
            $this->addError('cantidad', 'Stock insuficiente. Solo hay '.$producto->stock.' unidades.');

            return;
        }

        Movimiento::create([
            'producto_id' => $validated['productoId'],
            'user_id' => auth()->id(),
            'tipo' => $validated['tipo'],
            'cantidad' => $validated['cantidad'],
            'motivo' => $this->motivo ?: null,
            'fecha' => $validated['fecha'],
        ]);

        if ($validated['tipo'] === 'entrada') {
            $producto->increment('stock', $validated['cantidad']);
        } else {
            $producto->decrement('stock', $validated['cantidad']);
        }

        $nuevoStock = $producto->fresh()->stock;
        $estado = $nuevoStock <= 0 ? 'agotado' : ($nuevoStock <= $producto->stock_minimo ? 'bajo_stock' : 'en_stock');
        $producto->update(['estado' => $estado]);

        $this->mostrarModal = false;
        $this->dispatch('movimiento-guardado');
    }

    public function render(): View
    {
        return view('livewire.admin.movimiento.movimiento-form', [
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }
}
