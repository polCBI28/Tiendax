<?php

namespace App\Livewire\Admin\Categoria;

use App\Models\Categoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriaTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $estado = '';

    #[Url]
    public string $ordenar = 'nombre';

    #[Url]
    public string $dir = 'asc';

    public ?string $mensaje = null;

    /** @var array<int, string> */
    protected array $columnasOrdenables = ['nombre', 'created_at'];

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'estado', 'ordenar', 'dir'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'estado']);
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

    public function editar(int $categoriaId): void
    {
        $this->dispatch('abrir-formulario-categoria', categoriaId: $categoriaId);
    }

    public function eliminar(int $categoriaId): void
    {
        Categoria::findOrFail($categoriaId)->delete();

        $this->mensaje = 'Categoría eliminada correctamente.';
        $this->resetPage();
        $this->dispatch('categoria-eliminada');
    }

    #[On('categoria-guardada')]
    public function categoriaGuardada(): void
    {
        $this->mensaje = 'Categoría guardada correctamente.';
    }

    public function render(): View
    {
        $query = Categoria::withCount('productos');

        if ($this->estado !== '') {
            $query->where('activo', $this->estado === 'activo');
        }
        if ($this->search !== '') {
            $search = $this->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        if (in_array($this->ordenar, $this->columnasOrdenables)) {
            $query->orderBy($this->ordenar, $this->dir === 'desc' ? 'desc' : 'asc');
        }

        return view('livewire.admin.categoria.categoria-table', [
            'categorias' => $query->paginate(10),
        ]);
    }
}
