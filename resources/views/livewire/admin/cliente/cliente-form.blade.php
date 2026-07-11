<div>
    <flux:modal wire:model="mostrarModal" class="max-w-lg">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $clienteId ? 'Editar Cliente' : 'Nuevo Cliente' }}</flux:heading>
                <flux:subheading>Completa la información de contacto del cliente.</flux:subheading>
            </div>

            <flux:input wire:model="nombre" label="Nombre completo" required />
            <flux:input wire:model="documento" label="Documento (DNI/RUC)" />
            <flux:input wire:model="telefono" label="Teléfono" />
            <flux:input wire:model="email" label="Correo electrónico" type="email" />

            <div class="flex flex-col gap-3">
                <flux:button type="submit" variant="primary">
                    {{ $clienteId ? 'Guardar Cambios' : 'Guardar Cliente' }}
                </flux:button>
                <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
