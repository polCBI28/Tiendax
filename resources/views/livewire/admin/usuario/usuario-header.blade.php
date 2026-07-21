<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Usuarios</flux:heading>
        <flux:subheading>Administra las cuentas del sistema y sus roles asignados.</flux:subheading>
    </div>
    <div class="flex gap-3">
        <flux:button href="{{ route('roles.index') }}" wire:navigate icon="shield-check">
            Gestionar Roles
        </flux:button>
        <flux:button variant="primary" icon="plus" wire:click="crear">
            Nuevo Usuario
        </flux:button>
    </div>
</div>
