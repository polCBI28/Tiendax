<?php

namespace App\Livewire\Admin\Subcategoria;

use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SubcategoriaTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'categoria_id')]
    public string $categoriaId = '';

    #[Url]
    public string $ordenar = 'nombre';

    #[Url]
    public string $dir = 'asc';

    public ?string $mensaje = null;

    /** @var array<int, string> */
    protected array $columnasOrdenables = ['nombre', 'created_at'];

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'categoriaId', 'ordenar', 'dir'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'categoriaId']);
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

    public function editar(int $subcategoriaId): void
    {
        $this->dispatch('abrir-formulario-subcategoria', subcategoriaId: $subcategoriaId);
    }

    public function eliminar(int $subcategoriaId): void
    {
        Subcategoria::findOrFail($subcategoriaId)->delete();

        $this->mensaje = 'Subcategoría eliminada correctamente.';
        $this->resetPage();
        $this->dispatch('subcategoria-eliminada');
    }

    #[On('subcategoria-guardada')]
    public function subcategoriaGuardada(): void
    {
        $this->mensaje = 'Subcategoría guardada correctamente.';
    }

    public function render(): View
    {
        $query = Subcategoria::with('categoria')->withCount('productos');

        if ($this->categoriaId !== '') {
            $query->where('categoria_id', $this->categoriaId);
        }
        if ($this->search !== '') {
            $search = $this->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        if (in_array($this->ordenar, $this->columnasOrdenables)) {
            $query->orderBy($this->ordenar, $this->dir === 'desc' ? 'desc' : 'asc');
        }

        return view('livewire.admin.subcategoria.subcategoria-table', [
            'subcategorias' => $query->paginate(10),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }
}
