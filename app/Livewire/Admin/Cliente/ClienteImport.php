<?php

namespace App\Livewire\Admin\Cliente;

use App\Exports\ClientesExport;
use App\Imports\ClientesImport as ClientesImportClass;
use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClienteImport extends Component
{
    use WithFileUploads;

    public bool $mostrarModal = false;

    public $archivo;

    public ?int $creados = null;

    public ?int $actualizados = null;

    /** @var array<int, string> */
    public array $errores = [];

    #[On('abrir-importar-cliente')]
    public function abrir(): void
    {
        $this->reset(['archivo', 'creados', 'actualizados', 'errores']);
        $this->mostrarModal = true;
    }

    public function descargarPlantilla(): BinaryFileResponse
    {
        return (new ClientesExport(Cliente::query()->whereRaw('1 = 0')))
            ->download('plantilla-clientes.xlsx');
    }

    public function importar(): void
    {
        $this->validate(['archivo' => 'required|file|mimes:xlsx,csv,xls']);

        $import = new ClientesImportClass;
        Excel::import($import, $this->archivo->getRealPath());

        $this->creados = $import->creados;
        $this->actualizados = $import->actualizados;
        $this->errores = collect($import->failures())
            ->map(fn ($f) => "Fila {$f->row()}: ".implode(', ', $f->errors()))
            ->all();

        if ($this->creados > 0 || $this->actualizados > 0) {
            $this->dispatch('cliente-guardado');
        }

        $this->reset('archivo');
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
    }

    public function render(): View
    {
        return view('livewire.admin.cliente.cliente-import');
    }
}
