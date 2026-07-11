<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="p-0 overflow-hidden">
        <div class="flex flex-wrap items-end gap-4 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:input
                wire:model.live.debounce.400ms="search"
                icon="magnifying-glass"
                placeholder="Buscar subcategoría..."
                class="flex-1 min-w-[220px]"
            />
            <flux:select wire:model.live="categoriaId" placeholder="Categoría" class="w-48">
                <flux:select.option value="">Todas las categorías</flux:select.option>
                @foreach($categorias as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
                @endforeach
            </flux:select>
            @if($search !== '' || $categoriaId !== '')
                <flux:button variant="ghost" icon="x-mark" wire:click="limpiarFiltros">Limpiar filtros</flux:button>
            @endif
            <flux:spacer />
            <flux:text size="sm" class="text-zinc-400 whitespace-nowrap">{{ $subcategorias->total() }} subcategoría{{ $subcategorias->total() === 1 ? '' : 's' }}</flux:text>
        </div>

        <div wire:loading.class="opacity-60">
        <flux:table :paginate="$subcategorias" pagination:class="px-4 pb-4">
            <flux:table.columns>
                <flux:table.column class="w-[30%]" sortable :sorted="$ordenar === 'nombre'" :direction="$dir" wire:click="sort('nombre')">Subcategoría</flux:table.column>
                <flux:table.column class="w-[20%]">Categoría</flux:table.column>
                <flux:table.column class="w-[25%]">Descripción</flux:table.column>
                <flux:table.column align="center" class="w-[10%]">Productos</flux:table.column>
                <flux:table.column align="center" class="w-[7%]">Estado</flux:table.column>
                <flux:table.column align="center" class="w-[8%]">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($subcategorias as $sub)
                <flux:table.row wire:key="subcategoria-{{ $sub->id }}">
                    <flux:table.cell variant="strong">{{ $sub->nombre }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="zinc">{{ $sub->categoria->nombre ?? '—' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-zinc-500 truncate block max-w-[220px]">{{ $sub->descripcion ?? '—' }}</span>
                    </flux:table.cell>
                    <flux:table.cell align="center">{{ $sub->productos_count }}</flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge size="sm" :color="$sub->activo ? 'green' : 'zinc'">{{ $sub->activo ? 'Activo' : 'Inactivo' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <flux:button href="{{ route('subcategorias.show', $sub) }}" icon="eye" variant="ghost" size="sm" tooltip="Ver detalle" />
                            <flux:button wire:click="editar({{ $sub->id }})" icon="pencil" variant="ghost" size="sm" tooltip="Editar" />
                            <flux:button
                                wire:click="eliminar({{ $sub->id }})"
                                wire:confirm="¿Eliminar esta subcategoría? Esta acción no se puede deshacer."
                                icon="trash" variant="ghost" size="sm" tooltip="Eliminar"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <flux:icon.squares-2x2 class="size-12" />
                            <flux:text>No se encontraron subcategorías con los filtros aplicados.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </flux:card>

</div>
