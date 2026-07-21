<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Detalle de Productos</flux:heading>
        <flux:subheading>Características completas, costos, precios y rendimiento de ventas.</flux:subheading>
    </div>
    <div class="flex gap-3">
        <flux:button icon="arrow-up-tray" wire:click="importar">
            Importar
        </flux:button>
        <flux:button variant="primary" icon="plus" wire:click="crear">
            Nuevo Producto
        </flux:button>
    </div>
</div>
