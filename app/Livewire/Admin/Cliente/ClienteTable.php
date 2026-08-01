<?php

namespace App\Livewire\Admin\Cliente;

use App\Exports\ClientesExport;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClienteTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $ordenar = 'nombre';

    #[Url]
    public string $dir = 'asc';

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
        abort_unless(auth()->user()->can('clientes.editar'), 403);

        $this->dispatch('abrir-formulario-cliente', clienteId: $clienteId);
    }

    public function eliminar(int $clienteId): void
    {
        abort_unless(auth()->user()->can('clientes.eliminar'), 403);

        Cliente::findOrFail($clienteId)->delete();

        Flux::toast(text: 'Cliente eliminado correctamente.', variant: 'success');
        $this->resetPage();
        $this->dispatch('cliente-eliminado');
    }

    #[On('cliente-guardado')]
    public function clienteGuardado(): void
    {
        Flux::toast(text: 'Cliente guardado correctamente.', variant: 'success');
    }

    protected function filteredQuery(): Builder
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

        return $query;
    }

    public function exportarExcel(): BinaryFileResponse
    {
        abort_unless(auth()->user()->can('clientes.ver'), 403);

        return (new ClientesExport($this->filteredQuery()))
            ->download('clientes-'.now()->format('Y-m-d').'.xlsx');
    }

    public function exportarPdf(): StreamedResponse
    {
        abort_unless(auth()->user()->can('clientes.ver'), 403);

        $clientes = $this->filteredQuery()->get();
        $pdf = Pdf::loadView('exports.clientes-pdf', ['clientes' => $clientes]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'clientes-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function render(): View
    {
        return view('livewire.admin.cliente.cliente-table', [
            'clientes' => $this->filteredQuery()->paginate(10),
        ]);
    }
}
