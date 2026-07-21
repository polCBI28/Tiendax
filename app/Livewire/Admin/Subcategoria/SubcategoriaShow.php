<?php

namespace App\Livewire\Admin\Subcategoria;

use App\Models\Subcategoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar', ['title' => 'Subcategoría'])]
class SubcategoriaShow extends Component
{
    public Subcategoria $subcategoria;

    public function mount(Subcategoria $subcategoria): void
    {
        $subcategoria->load('categoria', 'productos');

        $this->subcategoria = $subcategoria;
    }

    public function render(): View
    {
        return view('livewire.admin.subcategoria.subcategoria-show');
    }
}
