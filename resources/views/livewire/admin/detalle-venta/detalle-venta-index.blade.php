<div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 0ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Total Líneas</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $totalLineas }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <flux:icon.receipt-percent variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 80ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Unidades Totales</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ number_format($unidadesTotales) }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shrink-0">
                    <flux:icon.archive-box variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 160ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Ingresos Totales</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-emerald-600 dark:text-emerald-400">S/ {{ number_format($ingresosTotales, 2) }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <flux:icon.banknotes variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>
    </div>

    <livewire:admin.detalle-venta.detalle-venta-header />
    <livewire:admin.detalle-venta.detalle-venta-table />
    <livewire:admin.detalle-venta.detalle-venta-form />

</div>
