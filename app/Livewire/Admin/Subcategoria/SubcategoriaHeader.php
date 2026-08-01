<?php

namespace App\Livewire\Admin\Subcategoria;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SubcategoriaHeader extends Component
{
    public function crear(): void
    {
        abort_unless(auth()->user()->can('categorias.crear'), 403);

        $this->dispatch('abrir-formulario-subcategoria');
    }

    public function importar(): void
    {
        abort_unless(auth()->user()->can('categorias.crear'), 403);

        $this->dispatch('abrir-importar-subcategoria');
    }

    public function render(): View
    {
        return view('livewire.admin.subcategoria.subcategoria-header');
    }
}
