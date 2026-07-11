<div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 0ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Total Productos</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $totalProductos }}</flux:heading>
                    <flux:text size="sm" class="text-blue-600 dark:text-blue-400 mt-1">+{{ $nuevosEsteMes }} este mes</flux:text>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <flux:icon.archive-box variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 80ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Bajo Stock</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-amber-600 dark:text-amber-400">{{ $bajoStock }}</flux:heading>
                    <flux:text size="sm" class="text-amber-600 dark:text-amber-400 mt-1">Acción requerida</flux:text>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 shrink-0">
                    <flux:icon.exclamation-triangle variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 160ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Valor Total</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-emerald-600 dark:text-emerald-400">S/ {{ number_format($valorTotal, 0) }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Actualizado hoy</flux:text>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <flux:icon.banknotes variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 240ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Agotados</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-red-600 dark:text-red-400">{{ $agotados }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Inactivos</flux:text>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400 shrink-0">
                    <flux:icon.archive-box-x-mark variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>
    </div>

    <livewire:admin.producto.producto-header />
    <livewire:admin.producto.producto-table />
    <livewire:admin.producto.producto-form />

</div>
