<div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 0ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Roles Definidos</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $totalRoles }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shrink-0">
                    <flux:icon.shield-check variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="animate-fade-in-up hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300" style="animation-delay: 80ms">
            <div class="flex items-start justify-between">
                <div>
                    <flux:subheading>Permisos en el Catálogo</flux:subheading>
                    <flux:heading size="xl" class="mt-1 text-blue-600 dark:text-blue-400">{{ $totalPermisos }}</flux:heading>
                </div>
                <div class="flex items-center justify-center size-11 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <flux:icon.key variant="solid" class="size-6" />
                </div>
            </div>
        </flux:card>
    </div>

    <livewire:admin.rol.rol-header />
    <livewire:admin.rol.rol-table />
    <livewire:admin.rol.rol-form />

</div>
