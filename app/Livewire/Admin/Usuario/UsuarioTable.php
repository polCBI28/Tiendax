<?php

namespace App\Livewire\Admin\Usuario;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UsuarioTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'rol')]
    public string $rolFiltro = '';

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'rolFiltro'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'rolFiltro']);
        $this->resetPage();
    }

    public function editar(int $usuarioId): void
    {
        $this->dispatch('abrir-formulario-usuario', usuarioId: $usuarioId);
    }

    public function eliminar(int $usuarioId): void
    {
        if ($usuarioId === auth()->id()) {
            Flux::toast(text: 'No puedes eliminar tu propio usuario.', variant: 'danger');

            return;
        }

        User::findOrFail($usuarioId)->delete();

        Flux::toast(text: 'Usuario eliminado correctamente.', variant: 'success');
        $this->resetPage();
        $this->dispatch('usuario-eliminado');
    }

    #[On('usuario-guardado')]
    public function usuarioGuardado(): void
    {
        Flux::toast(text: 'Usuario guardado correctamente.', variant: 'success');
    }

    public function render(): View
    {
        $query = User::with('roles');

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        if ($this->rolFiltro !== '') {
            $query->role($this->rolFiltro);
        }

        return view('livewire.admin.usuario.usuario-table', [
            'usuarios' => $query->orderBy('name')->paginate(10),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
