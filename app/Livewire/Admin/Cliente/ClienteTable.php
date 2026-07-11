<?php

namespace App\Livewire\Admin\Cliente;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ClienteTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $ordenar = 'nombre';

    #[Url]
    public string $dir = 'asc';

    public ?string $mensaje = null;

    /** @var array<int, string> */
    protected array $columnasOrdenables = ['nombre', 'created_at'];

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'ordenar', 'dir'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search']);
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

    public function editar(int $clienteId): void
    {
        $this->dispatch('abrir-formulario-cliente', clienteId: $clienteId);
    }

    public function eliminar(int $clienteId): void
    {
        Cliente::findOrFail($clienteId)->delete();

        $this->mensaje = 'Cliente eliminado correctamente.';
        $this->resetPage();
        $this->dispatch('cliente-eliminado');
    }

    #[On('cliente-guardado')]
    public function clienteGuardado(): void
    {
        $this->mensaje = 'Cliente guardado correctamente.';
    }

    public function render(): View
    {
        $query = Cliente::query();

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(fn ($sq) => $sq->where('nombre', 'like', "%{$search}%")
                ->orWhere('documento', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if (in_array($this->ordenar, $this->columnasOrdenables)) {
            $query->orderBy($this->ordenar, $this->dir === 'desc' ? 'desc' : 'asc');
        }

        return view('livewire.admin.cliente.cliente-table', [
            'clientes' => $query->paginate(10),
        ]);
    }
}
