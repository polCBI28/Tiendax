<div>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <flux:heading size="xl">Detalle de Ventas</flux:heading>
            <flux:subheading>Historial detallado con productos, descuentos y rendimiento por venta.</flux:subheading>
        </div>
        <div class="flex gap-3 shrink-0">
            <flux:button href="{{ route('ventas.index') }}" wire:navigate icon="receipt-percent">Ver Registro</flux:button>
            <flux:button href="{{ route('ventas.index', ['crear' => 1]) }}" wire:navigate variant="primary" icon="shopping-bag">Nueva Venta</flux:button>
        </div>
    </div>

    <livewire:admin.venta.venta-detalle-table />

</div>
