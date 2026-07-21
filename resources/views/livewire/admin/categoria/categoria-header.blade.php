<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Catálogo de Categorías</flux:heading>
        <flux:subheading>Organiza y gestiona tu inventario a través de las divisiones principales del negocio.</flux:subheading>
    </div>
    <div class="flex gap-3">
        <flux:button icon="arrow-up-tray" wire:click="importar">
            Importar
        </flux:button>
        <flux:button variant="primary" icon="plus" wire:click="crear">
            Nueva Categoría
        </flux:button>
        <flux:button href="{{ route('subcategorias.index') }}" wire:navigate icon="squares-2x2" variant="filled">
            Gestionar Sub-tipos
        </flux:button>
    </div>
</div>
