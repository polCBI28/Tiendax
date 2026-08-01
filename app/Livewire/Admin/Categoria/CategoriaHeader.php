<?php

namespace App\Livewire\Admin\Categoria;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CategoriaHeader extends Component
{
    public function crear(): void
    {
        abort_unless(auth()->user()->can('categorias.crear'), 403);

        $this->dispatch('abrir-formulario-categoria');
    }

    public function importar(): void
    {
        abort_unless(auth()->user()->can('categorias.crear'), 403);

        $this->dispatch('abrir-importar-categoria');
    }

    public function render(): View
    {
        return view('livewire.admin.categoria.categoria-header');
    }
}
