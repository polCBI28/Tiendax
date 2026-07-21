<?php

namespace App\Livewire\Admin\Rol;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class RolHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-rol');
    }

    public function render(): View
    {
        return view('livewire.admin.rol.rol-header');
    }
}
