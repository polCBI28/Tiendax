<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="p-0 overflow-hidden">
        <div class="flex flex-wrap items-end gap-4 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:input
                wire:model.live.debounce.400ms="search"
                icon="magnifying-glass"
                placeholder="Buscar por nombre, documento o email..."
                class="flex-1 min-w-[260px]"
            />
            @if($search !== '')
                <flux:button variant="ghost" icon="x-mark" wire:click="limpiarFiltros">Limpiar</flux:button>
            @endif
            <flux:spacer />
            <flux:text size="sm" class="text-zinc-400 whitespace-nowrap">{{ $clientes->total() }} cliente{{ $clientes->total() === 1 ? '' : 's' }}</flux:text>
        </div>

        <div wire:loading.class="opacity-60">
        <flux:table :paginate="$clientes" pagination:class="px-4 pb-4">
            <flux:table.columns>
                <flux:table.column class="w-[30%]" sortable :sorted="$ordenar === 'nombre'" :direction="$dir" wire:click="sort('nombre')">Cliente</flux:table.column>
                <flux:table.column class="w-[20%]">Documento</flux:table.column>
                <flux:table.column class="w-[20%]">Teléfono</flux:table.column>
                <flux:table.column class="w-[20%]">Email</flux:table.column>
                <flux:table.column align="center" class="w-[10%]">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($clientes as $cliente)
                <flux:table.row wire:key="cliente-{{ $cliente->id }}">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-500/10 flex items-center justify-center shrink-0">
                                <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm uppercase">{{ substr($cliente->nombre, 0, 1) }}</span>
                            </div>
                            <p class="font-medium text-zinc-800 dark:text-white">{{ $cliente->nombre }}</p>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell variant="strong">{{ $cliente->documento ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $cliente->telefono ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $cliente->email ?? '—' }}</flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <flux:button href="{{ route('clientes.show', $cliente) }}" icon="eye" variant="ghost" size="sm" tooltip="Ver detalle" />
                            <flux:button wire:click="editar({{ $cliente->id }})" icon="pencil" variant="ghost" size="sm" tooltip="Editar" />
                            <flux:button
                                wire:click="eliminar({{ $cliente->id }})"
                                wire:confirm="¿Eliminar este cliente? Esta acción no se puede deshacer."
                                icon="trash" variant="ghost" size="sm" tooltip="Eliminar"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <flux:icon.user-group class="size-12" />
                            <flux:text>No se encontraron clientes con los filtros aplicados.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </flux:card>

</div>
