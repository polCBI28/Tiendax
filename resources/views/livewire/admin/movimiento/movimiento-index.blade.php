<div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 0ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Ventas Hoy</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $ventasHoy }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <flux:icon.shopping-bag variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 80ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Entradas Hoy</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-emerald-600 dark:text-emerald-400">{{ $entradasHoy }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <flux:icon.arrow-down-circle variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 160ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Salidas Hoy</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-amber-600 dark:text-amber-400">{{ $salidasHoy }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 shrink-0">
                    <flux:icon.arrow-up-circle variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 240ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Ajustes del Mes</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $ajustesMes }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shrink-0">
                    <flux:icon.wrench variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>
    </div>

    <livewire:admin.movimiento.movimiento-header />
    <livewire:admin.movimiento.movimiento-table />
    <livewire:admin.movimiento.movimiento-form />

</div>
