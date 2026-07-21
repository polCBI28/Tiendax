<div>
    <flux:modal wire:model="mostrarModal" class="max-w-2xl">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $rolId ? 'Editar Rol' : 'Nuevo Rol' }}</flux:heading>
                <flux:subheading>Define el nombre del rol y qué permisos tendrá.</flux:subheading>
            </div>

            <flux:input wire:model="nombre" label="Nombre del rol" required />

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">Permisos</flux:heading>
                    <flux:text size="sm" class="text-zinc-400">{{ count($permisosSeleccionados) }} seleccionados</flux:text>
                </div>

                <div class="max-h-72 overflow-y-auto space-y-4 border border-zinc-200 dark:border-white/10 rounded-lg p-4">
                    @forelse($permisosPorModulo as $modulo => $permisos)
                        <div>
                            <p class="text-xs uppercase tracking-wider text-zinc-400 mb-2">{{ ucfirst($modulo) }}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach($permisos as $permiso)
                                    <flux:checkbox
                                        wire:model="permisosSeleccionados"
                                        value="{{ $permiso->id }}"
                                        label="{{ \Illuminate\Support\Str::after($permiso->name, '.') }}"
                                    />
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <flux:text size="sm" class="text-zinc-400">Aún no hay permisos en el catálogo. Agrega el primero abajo.</flux:text>
                    @endforelse
                </div>
            </div>

            <div class="space-y-2">
                <flux:text size="sm" class="text-zinc-400">¿Necesitas un permiso que no está en la lista? Agrégalo aquí — quedará disponible para todos los roles.</flux:text>
                <div class="flex gap-2">
                    <flux:input wire:model="nuevoPermiso" placeholder="ej. inventario.exportar" class="flex-1" />
                    <flux:button type="button" wire:click="agregarPermiso" icon="plus">Agregar</flux:button>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <flux:button type="submit" variant="primary">
                    {{ $rolId ? 'Guardar Cambios' : 'Crear Rol' }}
                </flux:button>
                <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
