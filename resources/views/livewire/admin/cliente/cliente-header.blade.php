<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Clientes</flux:heading>
        <flux:subheading>Gestiona la información de contacto y el historial de tus clientes.</flux:subheading>
    </div>
    <div class="flex gap-3">
        <flux:button icon="arrow-up-tray" wire:click="importar">
            Importar
        </flux:button>
        <flux:button variant="primary" icon="plus" wire:click="crear">
            Nuevo Cliente
        </flux:button>
    </div>
</div>
