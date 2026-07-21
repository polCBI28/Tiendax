<?php

namespace App\Livewire\Admin\Rol;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app.sidebar', ['title' => 'Roles'])]
class RolIndex extends Component
{
    #[On('rol-guardado')]
    #[On('rol-eliminado')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }

    public function render(): View
    {
        return view('livewire.admin.rol.rol-index', [
            'totalRoles' => Role::count(),
            'totalPermisos' => Permission::count(),
        ]);
    }
}
