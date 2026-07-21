<?php

namespace App\Livewire\Admin\Usuario;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class UsuarioHeader extends Component
{
    public function crear(): void
    {
        $this->dispatch('abrir-formulario-usuario');
    }

    public function render(): View
    {
        return view('livewire.admin.usuario.usuario-header');
    }
}
