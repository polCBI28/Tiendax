<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Reportes</flux:heading>
        <flux:subheading>Analítica de ventas del negocio.</flux:subheading>
    </div>
    <flux:button href="{{ route('reportes.exportar') }}" icon="arrow-down-tray" variant="primary">
        Exportar CSV
    </flux:button>
</div>
