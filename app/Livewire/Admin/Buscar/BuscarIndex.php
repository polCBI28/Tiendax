<?php

namespace App\Livewire\Admin\Buscar;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Búsqueda'])]
class BuscarIndex extends Component
{
    public function render(): View
    {
        return view('livewire.admin.buscar.buscar-index');
    }
}
