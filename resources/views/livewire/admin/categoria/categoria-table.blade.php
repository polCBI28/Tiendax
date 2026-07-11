<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="p-0 overflow-hidden">
        {{-- Toolbar: buscador + filtros --}}
        <div class="flex flex-wrap items-end gap-4 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:input
                wire:model.live.debounce.400ms="search"
                icon="magnifying-glass"
                placeholder="Buscar categoría..."
                class="flex-1 min-w-[220px]"
            />
            <flux:select wire:model.live="estado" placeholder="Estado" class="w-40">
                <flux:select.option value="">Todos los estados</flux:select.option>
                <flux:select.option value="activo">Activo</flux:select.option>
                <flux:select.option value="inactivo">Inactivo</flux:select.option>
            </flux:select>
            @if($search !== '' || $estado !== '')
                <flux:button variant="ghost" icon="x-mark" wire:click="limpiarFiltros">Limpiar filtros</flux:button>
            @endif
            <flux:spacer />
            <flux:text size="sm" class="text-zinc-400 whitespace-nowrap">{{ $categorias->total() }} categoría{{ $categorias->total() === 1 ? '' : 's' }}</flux:text>
        </div>

        {{-- Tabla --}}
        <div wire:loading.class="opacity-60">
        <flux:table :paginate="$categorias" pagination:class="px-4 pb-4">
            <flux:table.columns>
                <flux:table.column class="w-[35%]" sortable :sorted="$ordenar === 'nombre'" :direction="$dir" wire:click="sort('nombre')">Categoría</flux:table.column>
                <flux:table.column class="w-[35%]">Descripción</flux:table.column>
                <flux:table.column align="center" class="w-[12%]">Productos</flux:table.column>
                <flux:table.column align="center" class="w-[8%]">Estado</flux:table.column>
                <flux:table.column align="center" class="w-[10%]">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($categorias as $categoria)
                <flux:table.row wire:key="categoria-{{ $categoria->id }}">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">{{ $categoria->icono ?? 'category' }}</span>
                            </div>
                            <p class="font-medium text-zinc-800 dark:text-white">{{ $categoria->nombre }}</p>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-zinc-500 truncate block max-w-[280px]">{{ $categoria->descripcion ?? '—' }}</span>
                    </flux:table.cell>
                    <flux:table.cell align="center">{{ $categoria->productos_count }}</flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge size="sm" :color="$categoria->activo ? 'green' : 'zinc'">{{ $categoria->activo ? 'Activo' : 'Inactivo' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <flux:button href="{{ route('categorias.show', $categoria) }}" icon="eye" variant="ghost" size="sm" tooltip="Ver detalle" />
                            <flux:button wire:click="editar({{ $categoria->id }})" icon="pencil" variant="ghost" size="sm" tooltip="Editar" />
                            <flux:button
                                wire:click="eliminar({{ $categoria->id }})"
                                wire:confirm="¿Eliminar esta categoría? Esta acción no se puede deshacer."
                                icon="trash" variant="ghost" size="sm" tooltip="Eliminar"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <flux:icon.tag class="size-12" />
                            <flux:text>No se encontraron categorías con los filtros aplicados.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </flux:card>

</div>
