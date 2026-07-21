<div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 0ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Total Clientes</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $totalClientes }}</flux:heading>
                    <flux:text size="sm" class="text-blue-600 dark:text-blue-400 mt-1">+{{ $clientesEsteMes }} este mes</flux:text>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <flux:icon.user-group variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 80ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Con Compras</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-emerald-600 dark:text-emerald-400">{{ $conCompras }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Clientes recurrentes</flux:text>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <flux:icon.shopping-bag variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 160ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Con Documento</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $conDocumento }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">DNI/RUC registrado</flux:text>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shrink-0">
                    <flux:icon.identification variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>
    </div>

    <livewire:admin.cliente.cliente-header />
    <livewire:admin.cliente.cliente-table />
    <livewire:admin.cliente.cliente-form />
    <livewire:admin.cliente.cliente-import />

</div>
