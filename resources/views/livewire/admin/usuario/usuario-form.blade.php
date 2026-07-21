<div>
    <flux:modal wire:model="mostrarModal" class="max-w-lg">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $usuarioId ? 'Editar Usuario' : 'Nuevo Usuario' }}</flux:heading>
                <flux:subheading>Completa los datos de la cuenta y asigna un rol.</flux:subheading>
            </div>

            <flux:input wire:model="name" label="Nombre completo" required />
            <flux:input wire:model="email" label="Correo electrónico" type="email" required />
            <flux:input
                wire:model="password"
                label="Contraseña"
                type="password"
                :description="$usuarioId ? 'Déjalo en blanco para mantener la contraseña actual.' : null"
                :required="! $usuarioId"
            />

            <flux:select wire:model="rolId" label="Rol" placeholder="Seleccionar rol..." required>
                @foreach($roles as $rol)
                    <flux:select.option value="{{ $rol->id }}">{{ $rol->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex flex-col gap-3">
                <flux:button type="submit" variant="primary">
                    {{ $usuarioId ? 'Guardar Cambios' : 'Crear Usuario' }}
                </flux:button>
                <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
