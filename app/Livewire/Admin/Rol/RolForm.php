<?php

namespace App\Livewire\Admin\Rol;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolForm extends Component
{
    public bool $mostrarModal = false;

    public ?int $rolId = null;

    public string $nombre = '';

    /** @var array<int, string> */
    public array $permisosSeleccionados = [];

    public string $nuevoPermiso = '';

    #[On('abrir-formulario-rol')]
    public function abrir(?int $rolId = null): void
    {
        $this->reset(['rolId', 'nombre', 'permisosSeleccionados', 'nuevoPermiso']);
        $this->resetValidation();

        if ($rolId) {
            $rol = Role::with('permissions')->findOrFail($rolId);
            $this->rolId = $rol->id;
            $this->nombre = $rol->name;
            $this->permisosSeleccionados = $rol->permissions->pluck('id')->map(fn ($id) => (string) $id)->all();
        }

        $this->mostrarModal = true;
    }

    public function agregarPermiso(): void
    {
        $nombre = Str::of($this->nuevoPermiso)->trim()->lower()->replace(' ', '-')->toString();

        $this->validate([
            'nuevoPermiso' => ['required', 'string', 'max:125'],
        ], [], ['nuevoPermiso' => 'nombre del permiso']);

        if ($nombre === '') {
            return;
        }

        $permiso = Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);

        $this->permisosSeleccionados[] = (string) $permiso->id;
        $this->nuevoPermiso = '';
    }

    public function guardar(): void
    {
        $validated = $this->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->rolId)],
        ]);

        if ($this->rolId) {
            $rol = Role::findOrFail($this->rolId);
            $rol->update(['name' => $validated['nombre']]);
        } else {
            $rol = Role::create(['name' => $validated['nombre'], 'guard_name' => 'web']);
        }

        $rol->syncPermissions(Permission::whereIn('id', $this->permisosSeleccionados)->get());

        $this->mostrarModal = false;
        $this->dispatch('rol-guardado');
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
    }

    public function render(): View
    {
        $permisosPorModulo = Permission::orderBy('name')->get()->groupBy(function ($permiso) {
            return Str::before($permiso->name, '.');
        });

        return view('livewire.admin.rol.rol-form', [
            'permisosPorModulo' => $permisosPorModulo,
        ]);
    }
}
