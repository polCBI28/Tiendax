<?php

namespace App\Livewire\Admin\Categoria;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CategoriaHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-categoria');
    }

    public function importar(): void
    {
        $this->dispatch('abrir-importar-categoria');
    }

    public function render(): View
    {
        return view('livewire.admin.categoria.categoria-header');
    }
}
