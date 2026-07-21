<?php

namespace App\Livewire\Admin\Producto;

use App\Exports\ProductosExport;
use App\Imports\ProductosImport;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductoImport extends Component
{
    use WithFileUploads;

    public bool $mostrarModal = false;

    public $archivo;

    public ?int $creados = null;

    public ?int $actualizados = null;

    /** @var array<int, string> */
    public array $errores = [];

    #[On('abrir-importar-producto')]
    public function abrir(): void
    {
        $this->reset(['archivo', 'creados', 'actualizados', 'errores']);
        $this->mostrarModal = true;
    }

    public function descargarPlantilla(): BinaryFileResponse
    {
        return (new ProductosExport(Producto::query()->whereRaw('1 = 0')))
            ->download('plantilla-productos.xlsx');
    }

    public function importar(): void
    {
        $this->validate(['archivo' => 'required|file|mimes:xlsx,csv,xls']);

        $import = new ProductosImport;
        Excel::import($import, $this->archivo->getRealPath());

        $this->creados = $import->creados;
        $this->actualizados = $import->actualizados;
        $this->errores = collect($import->failures())
            ->map(fn ($f) => "Fila {$f->row()}: ".implode(', ', $f->errors()))
            ->all();

        if ($this->creados > 0 || $this->actualizados > 0) {
            $this->dispatch('producto-guardado');
        }

        $this->reset('archivo');
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
    }

    public function render(): View
    {
        return view('livewire.admin.producto.producto-import');
    }
}
