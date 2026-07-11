<?php

namespace App\Livewire\Admin\Buscar;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class BuscarTable extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    public function render(): View
    {
        $q = trim($this->search);

        if (strlen($q) < 2) {
            return view('livewire.admin.buscar.buscar-table', [
                'q' => $q,
                'productos' => collect(),
                'clientes' => collect(),
                'ventas' => collect(),
            ]);
        }

        return view('livewire.admin.buscar.buscar-table', [
            'q' => $q,
            'productos' => Producto::where('nombre', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%")
                ->with('categoria')
                ->limit(8)
                ->get(),
            'clientes' => Cliente::where('nombre', 'like', "%{$q}%")
                ->orWhere('documento', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->limit(8)
                ->get(),
            'ventas' => Venta::where('numero_boleta', 'like', "%{$q}%")
                ->with('cliente')
                ->limit(8)
                ->get(),
        ]);
    }
}
