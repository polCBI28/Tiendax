<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Roles y Permisos</flux:heading>
        <flux:subheading>Crea roles y define qué puede hacer cada uno en el sistema.</flux:subheading>
    </div>
    <div class="flex gap-3">
        <flux:button href="{{ route('usuarios.index') }}" wire:navigate icon="users">
            Ver Usuarios
        </flux:button>
        <flux:button variant="primary" icon="plus" wire:click="crear">
            Nuevo Rol
        </flux:button>
    </div>
</div>
