<?php

namespace App\Livewire\Admin\Usuario;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app.sidebar', ['title' => 'Usuarios'])]
class UsuarioIndex extends Component
{
    #[On('usuario-guardado')]
    #[On('usuario-eliminado')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        return view('livewire.admin.usuario.usuario-index', [
            'totalUsuarios' => User::count(),
            'totalRoles' => Role::count(),
            'superAdmins' => User::role('Super Admin')->count(),
        ]);
    }
}
