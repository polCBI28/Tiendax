<?php

namespace App\Livewire\Admin\Rol;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RolTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function editar(int $rolId): void
    {
        $this->dispatch('abrir-formulario-rol', rolId: $rolId);
    }

    public function eliminar(int $rolId): void
    {
        $rol = Role::findOrFail($rolId);

        if ($rol->name === 'Super Admin') {
            Flux::toast(text: 'El rol "Super Admin" no se puede eliminar.', variant: 'danger');

            return;
        }

        $rol->delete();

        Flux::toast(text: 'Rol eliminado correctamente.', variant: 'success');
        $this->resetPage();
        $this->dispatch('rol-eliminado');
    }

    #[On('rol-guardado')]
    public function rolGuardado(): void
    {
        Flux::toast(text: 'Rol guardado correctamente.', variant: 'success');
    }

    public function render(): View
    {
        $query = Role::withCount('permissions', 'users');

        if ($this->search !== '') {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return view('livewire.admin.rol.rol-table', [
            'roles' => $query->orderBy('name')->paginate(10),
        ]);
    }
}
