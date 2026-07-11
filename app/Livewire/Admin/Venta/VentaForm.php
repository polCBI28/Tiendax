<?php

namespace App\Livewire\Admin\Venta;

use App\Models\Categoria;
use App\Models\DetalleVenta;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class VentaForm extends Component
{
    public bool $mostrarModal = false;

    public string $fechaVenta = '';

    public string $descripcion = '';

    public string $search = '';

    public ?int $categoriaFiltro = null;

    /** @var array<int, array{nombre: string, precio: float, stock: int, cantidad: int, adicional: float, mostrarAdicional: bool}> */
    public array $carrito = [];

    public bool $descuentoActivo = false;

    public string $descuentoTipo = 'monto';

    public float $descuentoValor = 0;

    public bool $recargoActivo = false;

    public string $recargoTipo = 'monto';

    public float $recargoValor = 0;

    public float $adelanto = 0;

    #[Computed]
    public function categorias()
    {
        return Categoria::where('activo', true)->orderBy('nombre')->get();
    }

    #[Computed]
    public function productosDisponibles()
    {
        $query = Producto::where('activo', true)->where('stock', '>', 0);

        if ($this->categoriaFiltro) {
            $query->where('categoria_id', $this->categoriaFiltro);
        }

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(fn ($q) => $q->where('nombre', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
        }

        return $query->orderBy('nombre')->limit(24)->get();
    }

    #[Computed]
    public function totales(): array
    {
        $subtotal = collect($this->carrito)->sum(fn ($item) => ($item['precio'] + $item['adicional']) * $item['cantidad']);

        $descuento = 0;
        if ($this->descuentoActivo && $this->descuentoValor > 0) {
            if ($this->descuentoTipo === 'porcentaje') {
                $valor = min($this->descuentoValor, 100);
                $descuento = round($subtotal * $valor / 100, 2);
            } else {
                $descuento = min($this->descuentoValor, $subtotal);
            }
        }

        $base = $subtotal - $descuento;
        $recargo = 0;
        if ($this->recargoActivo && $this->recargoValor > 0) {
            if ($this->recargoTipo === 'porcentaje') {
                $recargo = round($base * min($this->recargoValor, 100) / 100, 2);
            } else {
                $recargo = $this->recargoValor;
            }
        }

        $total = $base + $recargo;
        $adelantoAplicado = min($this->adelanto, $total);
        $deuda = max(0, $total - $adelantoAplicado);

        return compact('subtotal', 'descuento', 'recargo', 'total', 'adelantoAplicado', 'deuda');
    }

    #[Computed]
    public function numeroBoletaPreview(): string
    {
        return 'B001-'.str_pad((Venta::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
    }

    #[On('abrir-formulario-venta')]
    public function abrir(): void
    {
        $this->resetValidation();
        $this->reset([
            'descripcion', 'search', 'categoriaFiltro', 'carrito',
            'descuentoActivo', 'descuentoTipo', 'descuentoValor',
            'recargoActivo', 'recargoTipo', 'recargoValor', 'adelanto',
        ]);
        $this->fechaVenta = now()->format('Y-m-d');
        $this->mostrarModal = true;
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
        $this->resetValidation();
    }

    public function filtrarCategoria(?int $categoriaId): void
    {
        $this->categoriaFiltro = $categoriaId;
    }

    public function agregarProducto(int $productoId): void
    {
        $producto = Producto::find($productoId);

        if (! $producto) {
            return;
        }

        if (isset($this->carrito[$productoId])) {
            if ($this->carrito[$productoId]['cantidad'] < $producto->stock) {
                $this->carrito[$productoId]['cantidad']++;
            }
        } else {
            $this->carrito[$productoId] = [
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio_venta,
                'stock' => $producto->stock,
                'cantidad' => 1,
                'adicional' => 0,
                'mostrarAdicional' => false,
            ];
        }
    }

    public function cambiarCantidad(int $productoId, int $delta): void
    {
        if (! isset($this->carrito[$productoId])) {
            return;
        }

        $this->carrito[$productoId]['cantidad'] += $delta;

        if ($this->carrito[$productoId]['cantidad'] <= 0) {
            unset($this->carrito[$productoId]);
        }
    }

    public function toggleAdicional(int $productoId): void
    {
        if (isset($this->carrito[$productoId])) {
            $this->carrito[$productoId]['mostrarAdicional'] = ! $this->carrito[$productoId]['mostrarAdicional'];
        }
    }

    public function quitarProducto(int $productoId): void
    {
        unset($this->carrito[$productoId]);
    }

    public function limpiarCarrito(): void
    {
        $this->carrito = [];
        $this->descuentoActivo = false;
        $this->descuentoValor = 0;
        $this->recargoActivo = false;
        $this->recargoValor = 0;
    }

    public function setAdelanto50(): void
    {
        $this->adelanto = round($this->totales['total'] / 2, 2);
    }

    public function guardar(string $estadoDeseado): void
    {
        if (empty($this->carrito)) {
            $this->addError('carrito', 'Agrega al menos un producto al carrito.');

            return;
        }

        $this->validate([
            'fechaVenta' => ['required', 'date'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        $totales = $this->totales;

        $estado = $estadoDeseado;
        if ($estado === 'completado' && $totales['adelantoAplicado'] > 0 && $totales['adelantoAplicado'] < $totales['total']) {
            $estado = 'pendiente';
        }

        DB::transaction(function () use ($totales, $estado) {
            $venta = Venta::create([
                'user_id' => auth()->id(),
                'numero_boleta' => $this->numeroBoletaPreview,
                'fecha_venta' => $this->fechaVenta,
                'descripcion' => $this->descripcion,
                'total' => $totales['total'],
                'adelanto' => $totales['adelantoAplicado'],
                'descuento_tipo' => $this->descuentoActivo && $this->descuentoValor > 0 ? $this->descuentoTipo : null,
                'descuento_valor' => $this->descuentoActivo && $this->descuentoValor > 0 ? $this->descuentoValor : 0,
                'recargo_tipo' => $this->recargoActivo && $this->recargoValor > 0 ? $this->recargoTipo : null,
                'recargo_valor' => $this->recargoActivo && $this->recargoValor > 0 ? $this->recargoValor : 0,
                'estado' => $estado,
            ]);

            foreach ($this->carrito as $productoId => $item) {
                $precioUnitario = $item['precio'] + $item['adicional'];

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $productoId,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'adicional' => $item['adicional'],
                    'subtotal' => $precioUnitario * $item['cantidad'],
                ]);

                $producto = Producto::find($productoId);
                $producto->decrement('stock', $item['cantidad']);
                $nuevoStock = $producto->fresh()->stock;
                $producto->update([
                    'estado' => $nuevoStock <= 0 ? 'agotado'
                        : ($nuevoStock <= $producto->stock_minimo ? 'bajo_stock' : 'en_stock'),
                ]);

                Movimiento::create([
                    'producto_id' => $productoId,
                    'user_id' => auth()->id(),
                    'tipo' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'motivo' => 'Venta '.$venta->numero_boleta,
                    'fecha' => $this->fechaVenta,
                ]);
            }
        });

        $this->mostrarModal = false;
        $this->dispatch('venta-guardada');
    }

    public function render(): View
    {
        return view('livewire.admin.venta.venta-form');
    }
}
