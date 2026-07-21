<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Reportes</flux:heading>
        <flux:subheading>Analítica de ventas del negocio.</flux:subheading>
    </div>
    <div class="flex gap-3">
        <flux:button wire:click="exportarCsv" icon="arrow-down-tray">
            Exportar CSV
        </flux:button>
        <flux:dropdown position="bottom" align="end">
            <flux:button icon="document-arrow-down" variant="primary">
                Generar PDF
            </flux:button>
            <flux:menu class="p-4 space-y-3 w-64">
                <flux:input wire:model="desde" type="date" label="Desde" />
                <flux:input wire:model="hasta" type="date" label="Hasta" />
                <flux:button wire:click="generarPdf" variant="primary" class="w-full justify-center">
                    Descargar PDF
                </flux:button>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>
