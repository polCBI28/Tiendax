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
                placeholder="Buscar por nombre o SKU..."
                class="flex-1 min-w-[220px]"
            />
            <flux:select wire:model.live="categoriaId" placeholder="Categoría" class="w-44">
                <flux:select.option value="">Todas las categorías</flux:select.option>
                @foreach($categorias as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="estado" placeholder="Estado" class="w-40">
                <flux:select.option value="">Todos los estados</flux:select.option>
                <flux:select.option value="en_stock">En stock</flux:select.option>
                <flux:select.option value="bajo_stock">Bajo stock</flux:select.option>
                <flux:select.option value="agotado">Agotado</flux:select.option>
            </flux:select>
            @if($search !== '' || $categoriaId !== '' || $estado !== '')
                <flux:button variant="ghost" icon="x-mark" wire:click="limpiarFiltros">Limpiar filtros</flux:button>
            @endif
            <flux:spacer />
            <flux:text size="sm" class="text-zinc-400 whitespace-nowrap">{{ $productos->total() }} producto{{ $productos->total() === 1 ? '' : 's' }}</flux:text>
        </div>

        {{-- Tabla --}}
        <div wire:loading.class="opacity-60">
        <flux:table :paginate="$productos" pagination:class="px-4 pb-4">
            <flux:table.columns>
                <flux:table.column class="w-[26%]">Producto</flux:table.column>
                <flux:table.column class="w-[10%]" sortable :sorted="$ordenar === 'sku'" :direction="$dir" wire:click="sort('sku')">SKU</flux:table.column>
                <flux:table.column class="w-[15%]">Categoría</flux:table.column>
                <flux:table.column align="end" class="w-[14%]" sortable :sorted="$ordenar === 'precio_venta'" :direction="$dir" wire:click="sort('precio_venta')">Precio</flux:table.column>
                <flux:table.column align="end" class="w-[10%]" sortable :sorted="$ordenar === 'stock'" :direction="$dir" wire:click="sort('stock')">Stock</flux:table.column>
                <flux:table.column align="center" class="w-[10%]">Estado</flux:table.column>
                <flux:table.column align="end" class="w-[12%]" sortable :sorted="$ordenar === 'ingresos_generados'" :direction="$dir" wire:click="sort('ingresos_generados')">Ventas</flux:table.column>
                <flux:table.column align="center" class="w-[13%]">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($productos as $producto)
                @php
                    $margen = $producto->precio_costo > 0
                        ? round((($producto->precio_venta - $producto->precio_costo) / $producto->precio_venta) * 100, 1)
                        : null;
                    $margenColor = $margen === null ? 'zinc' : ($margen >= 30 ? 'green' : ($margen >= 15 ? 'blue' : 'red'));
                    $estadoMap = [
                        'en_stock' => ['label' => 'En stock', 'color' => 'green'],
                        'bajo_stock' => ['label' => 'Bajo stock', 'color' => 'amber'],
                        'agotado' => ['label' => 'Agotado', 'color' => 'red'],
                    ];
                    $e = $estadoMap[$producto->estado] ?? ['label' => $producto->estado, 'color' => 'zinc'];
                @endphp
                <flux:table.row wire:key="producto-{{ $producto->id }}">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}"
                                     alt="{{ $producto->nombre }}"
                                     class="w-9 h-9 rounded-lg object-cover border border-zinc-200 dark:border-white/10 shrink-0">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-zinc-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                                    <flux:icon.cube variant="micro" class="text-zinc-400" />
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate max-w-[180px] font-medium text-zinc-800 dark:text-white">{{ $producto->nombre }}</p>
                                @if($producto->descripcion)
                                <p class="truncate max-w-[180px] text-xs text-zinc-500">{{ $producto->descripcion }}</p>
                                @endif
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell variant="strong">{{ $producto->sku }}</flux:table.cell>
                    <flux:table.cell>
                        @if($producto->categoria)
                            <p class="text-zinc-700 dark:text-zinc-200">{{ $producto->categoria->nombre }}</p>
                            @if($producto->subcategoria)
                                <p class="text-xs text-zinc-400">{{ $producto->subcategoria->nombre }}</p>
                            @endif
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <p class="font-medium text-zinc-800 dark:text-white">S/ {{ number_format($producto->precio_venta, 2) }}</p>
                        <p class="text-xs text-zinc-400">
                            @if($producto->precio_costo)
                                costo S/ {{ number_format($producto->precio_costo, 2) }}
                            @endif
                            @if($margen !== null)
                                · <span class="{{ match(true) { $margenColor === 'green' => 'text-green-600 dark:text-green-400', $margenColor === 'blue' => 'text-blue-600 dark:text-blue-400', $margenColor === 'red' => 'text-red-600 dark:text-red-400', default => '' } }}">{{ $margen }}%</span>
                            @endif
                        </p>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        {{ $producto->stock }} <span class="text-zinc-400">/ {{ $producto->stock_minimo }}</span>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge size="sm" :color="$e['color']">{{ $e['label'] }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <p class="font-medium text-zinc-800 dark:text-white">S/ {{ number_format($producto->ingresos_generados ?? 0, 2) }}</p>
                        <p class="text-xs text-zinc-400">{{ number_format($producto->unidades_vendidas ?? 0) }} uds</p>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <flux:button href="{{ route('productos.show', $producto) }}" icon="eye" variant="ghost" size="sm" tooltip="Ver detalle" />
                            <flux:button wire:click="editar({{ $producto->id }})" icon="pencil" variant="ghost" size="sm" tooltip="Editar" />
                            <flux:button
                                wire:click="eliminar({{ $producto->id }})"
                                wire:confirm="¿Eliminar este producto? Esta acción no se puede deshacer."
                                icon="trash" variant="ghost" size="sm" tooltip="Eliminar"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <flux:icon.cube-transparent class="size-12" />
                            <flux:text>No se encontraron productos con los filtros aplicados.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </flux:card>

</div>
