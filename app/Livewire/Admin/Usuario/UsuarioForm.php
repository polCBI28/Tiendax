<?php

namespace App\Livewire\Admin\Usuario;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UsuarioForm extends Component
{
    public bool $mostrarModal = false;

    public ?int $usuarioId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $rolId = '';

    #[On('abrir-formulario-usuario')]
    public function abrir(?int $usuarioId = null): void
    {
        $this->reset(['usuarioId', 'name', 'email', 'password', 'rolId']);
        $this->resetValidation();

        if ($usuarioId) {
            $usuario = User::with('roles')->findOrFail($usuarioId);
            $this->usuarioId = $usuario->id;
            $this->name = $usuario->name;
            $this->email = $usuario->email;
            $this->rolId = (string) $usuario->roles->first()?->id;
        }

        $this->mostrarModal = true;
    }

    public function guardar(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->usuarioId)],
            'password' => [$this->usuarioId ? 'nullable' : 'required', 'string', 'min:8'],
            'rolId' => ['required', 'exists:roles,id'],
        ]);

        $datos = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $datos['password'] = bcrypt($validated['password']);
        }

        if ($this->usuarioId) {
            $usuario = User::findOrFail($this->usuarioId);
            $usuario->update($datos);
        } else {
            $usuario = User::create($datos);
        }

        $usuario->syncRoles([Role::findOrFail($validated['rolId'])]);

        $this->mostrarModal = false;
        $this->dispatch('usuario-guardado');
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
    }

    public function render(): View
    {
        return view('livewire.admin.usuario.usuario-form', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
