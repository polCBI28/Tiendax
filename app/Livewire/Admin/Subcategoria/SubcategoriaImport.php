<?php

namespace App\Livewire\Admin\Subcategoria;

use App\Exports\SubcategoriasExport;
use App\Imports\SubcategoriasImport as SubcategoriasImportClass;
use App\Models\Subcategoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SubcategoriaImport extends Component
{
    use WithFileUploads;

    public bool $mostrarModal = false;

    public $archivo;

    public ?int $creados = null;

    public ?int $actualizados = null;

    /** @var array<int, string> */
    public array $errores = [];

    #[On('abrir-importar-subcategoria')]
    public function abrir(): void
    {
        $this->reset(['archivo', 'creados', 'actualizados', 'errores']);
        $this->mostrarModal = true;
    }

    public function descargarPlantilla(): BinaryFileResponse
    {
        return (new SubcategoriasExport(Subcategoria::query()->whereRaw('1 = 0')))
            ->download('plantilla-subcategorias.xlsx');
    }

    public function importar(): void
    {
        $this->validate(['archivo' => 'required|file|mimes:xlsx,csv,xls']);

        $import = new SubcategoriasImportClass;
        Excel::import($import, $this->archivo->getRealPath());

        $this->creados = $import->creados;
        $this->actualizados = $import->actualizados;
        $this->errores = collect($import->failures())
            ->map(fn ($f) => "Fila {$f->row()}: ".implode(', ', $f->errors()))
            ->all();

        if ($this->creados > 0 || $this->actualizados > 0) {
            $this->dispatch('subcategoria-guardada');
        }

        $this->reset('archivo');
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
    }

    public function render(): View
    {
        return view('livewire.admin.subcategoria.subcategoria-import');
    }
}
