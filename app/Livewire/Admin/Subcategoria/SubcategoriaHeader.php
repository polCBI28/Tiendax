<?php

namespace App\Livewire\Admin\Subcategoria;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SubcategoriaHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-subcategoria');
    }

    public function render(): View
    {
        return view('livewire.admin.subcategoria.subcategoria-header');
    }
}
