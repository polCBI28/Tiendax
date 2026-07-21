<?php

namespace App\Livewire\Admin\Subcategoria;

use App\Exports\SubcategoriasExport;
use App\Models\Categoria;
use App\Models\Subcategoria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    protected function filteredQuery(): Builder
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

        return $query;
    }

    public function exportarExcel(): BinaryFileResponse
    {
        return (new SubcategoriasExport($this->filteredQuery()))
            ->download('subcategorias-'.now()->format('Y-m-d').'.xlsx');
    }

    public function exportarPdf(): StreamedResponse
    {
        $subcategorias = $this->filteredQuery()->get();
        $pdf = Pdf::loadView('exports.subcategorias-pdf', ['subcategorias' => $subcategorias]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'subcategorias-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function render(): View
    {
        return view('livewire.admin.subcategoria.subcategoria-table', [
            'subcategorias' => $this->filteredQuery()->paginate(10),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }
}
