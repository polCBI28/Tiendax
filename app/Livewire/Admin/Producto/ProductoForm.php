<?php

namespace App\Livewire\Admin\Producto;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Subcategoria;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductoForm extends Component
{
    use WithFileUploads;

    public bool $mostrarModal = false;

    public ?int $productoId = null;

    public string $nombre = '';

    public string $sku = '';

    public ?int $categoriaId = null;

    public ?int $subcategoriaId = null;

    public string $descripcion = '';

    public ?float $precioVenta = null;

    public ?float $precioCosto = null;

    public int $stock = 0;

    public int $stockMinimo = 5;

    public bool $activo = true;

    public $imagen = null;

    public ?string $imagenActual = null;

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', Rule::unique('productos', 'sku')->ignore($this->productoId)],
            'categoriaId' => ['required', 'exists:categorias,id'],
            'subcategoriaId' => ['nullable', 'exists:subcategorias,id'],
            'precioVenta' => ['required', 'numeric', 'min:0'],
            'precioCosto' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stockMinimo' => ['required', 'integer', 'min:0'],
            'imagen' => ['nullable', 'image', 'max:2048'],
        ];
    }

    #[On('abrir-formulario-producto')]
    public function abrir(?int $productoId = null): void
    {
        $this->resetValidation();
        $this->productoId = $productoId;
        $this->imagen = null;

        if ($productoId) {
            $producto = Producto::findOrFail($productoId);
            $this->nombre = $producto->nombre;
            $this->sku = $producto->sku;
            $this->categoriaId = $producto->categoria_id;
            $this->subcategoriaId = $producto->subcategoria_id;
            $this->descripcion = $producto->descripcion ?? '';
            $this->precioVenta = $producto->precio_venta;
            $this->precioCosto = $producto->precio_costo;
            $this->stock = $producto->stock;
            $this->stockMinimo = $producto->stock_minimo;
            $this->activo = (bool) $producto->activo;
            $this->imagenActual = $producto->imagen;
        } else {
            $this->reset(['nombre', 'sku', 'categoriaId', 'subcategoriaId', 'descripcion', 'precioVenta', 'precioCosto', 'imagenActual']);
            $this->stock = 0;
            $this->stockMinimo = 5;
            $this->activo = true;
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
            'nombre' => $validated['nombre'],
            'sku' => $validated['sku'],
            'categoria_id' => $validated['categoriaId'],
            'subcategoria_id' => $validated['subcategoriaId'],
            'descripcion' => $this->descripcion,
            'precio_venta' => $validated['precioVenta'],
            'precio_costo' => $validated['precioCosto'],
            'stock' => $validated['stock'],
            'stock_minimo' => $validated['stockMinimo'],
            'activo' => $this->activo,
        ];

        $data['estado'] = $data['stock'] <= 0
            ? 'agotado'
            : ($data['stock'] <= $data['stock_minimo'] ? 'bajo_stock' : 'en_stock');

        if ($this->imagen) {
            $data['imagen'] = $this->imagen->store('productos', 'public');
        }

        if ($this->productoId) {
            Producto::findOrFail($this->productoId)->update($data);
        } else {
            Producto::create($data);
        }

        $this->mostrarModal = false;
        $this->dispatch('producto-guardado');
    }

    public function render(): View
    {
        return view('livewire.admin.producto.producto-form', [
            'categorias' => Categoria::where('activo', true)->orderBy('nombre')->get(),
            'subcategorias' => Subcategoria::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }
}
