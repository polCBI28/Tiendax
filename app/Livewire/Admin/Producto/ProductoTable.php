<?php

namespace App\Livewire\Admin\Producto;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductoTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'categoria_id')]
    public string $categoriaId = '';

    #[Url]
    public string $estado = '';

    #[Url]
    public string $ordenar = 'nombre';

    #[Url]
    public string $dir = 'asc';

    public ?string $mensaje = null;

    /** @var array<int, string> */
    protected array $columnasOrdenables = [
        'nombre', 'sku', 'precio_venta', 'precio_costo', 'stock', 'unidades_vendidas', 'ingresos_generados',
    ];

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'categoriaId', 'estado', 'ordenar', 'dir'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'categoriaId', 'estado']);
        $this->ordenar = 'nombre';
        $this->dir = 'asc';
        $this->resetPage();
    }

    public function sort(string $columna): void
    {
        if (! in_array($columna, $this->columnasOrdenables)) {
            return;
        }

        if ($this->ordenar === $columna) {
            $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenar = $columna;
            $this->dir = 'asc';
        }

        $this->resetPage();
    }

    public function editar(int $productoId): void
    {
        $this->dispatch('abrir-formulario-producto', productoId: $productoId);
    }

    public function eliminar(int $productoId): void
    {
        Producto::findOrFail($productoId)->delete();

        $this->mensaje = 'Producto eliminado correctamente.';
        $this->resetPage();
        $this->dispatch('producto-eliminado');
    }

    #[On('producto-guardado')]
    public function productoGuardado(): void
    {
        $this->mensaje = 'Producto guardado correctamente.';
    }

    public function render(): View
    {
        $query = Producto::with('categoria', 'subcategoria')
            ->withCount('detalleVentas as num_ventas')
            ->withSum('detalleVentas as unidades_vendidas', 'cantidad')
            ->withSum('detalleVentas as ingresos_generados', 'subtotal');

        if ($this->categoriaId !== '') {
            $query->where('categoria_id', $this->categoriaId);
        }
        if ($this->estado !== '') {
            $query->where('estado', $this->estado);
        }
        if ($this->search !== '') {
            $search = $this->search;
            $query->where(fn ($sq) => $sq->where('nombre', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
        }

        if (in_array($this->ordenar, $this->columnasOrdenables)) {
            $query->orderBy($this->ordenar, $this->dir === 'desc' ? 'desc' : 'asc');
        }

        return view('livewire.admin.producto.producto-table', [
            'productos' => $query->paginate(10),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }
}
