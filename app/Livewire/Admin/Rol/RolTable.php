<?php

namespace App\Livewire\Admin\Rol;

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

    public ?string $mensaje = null;

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
            $this->addError('protegido', 'El rol "Super Admin" no se puede eliminar.');

            return;
        }

        $rol->delete();

        $this->mensaje = 'Rol eliminado correctamente.';
        $this->resetPage();
        $this->dispatch('rol-eliminado');
    }

    #[On('rol-guardado')]
    public function rolGuardado(): void
    {
        $this->mensaje = 'Rol guardado correctamente.';
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
