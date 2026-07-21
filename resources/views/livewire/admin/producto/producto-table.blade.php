<div>
    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="overflow-hidden p-0">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:input
                wire:model.live.debounce.400ms="search"
                icon="magnifying-glass"
                placeholder="Buscar por nombre o SKU..."
                class="flex-1 min-w-[200px]"
            />
            
            <flux:select wire:model.live="categoriaId" placeholder="Categoría" class="w-40">
                <flux:select.option value="">Todas las categorías</flux:select.option>
                @foreach($categorias as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
                @endforeach
            </flux:select>
            
            <flux:select wire:model.live="estado" placeholder="Estado" class="w-36">
                <flux:select.option value="">Todos los estados</flux:select.option>
                <flux:select.option value="en_stock">En stock</flux:select.option>
                <flux:select.option value="bajo_stock">Bajo stock</flux:select.option>
                <flux:select.option value="agotado">Agotado</flux:select.option>
            </flux:select>

            <div class="flex items-center gap-2 ml-auto">
                @if($search !== '' || $categoriaId !== '' || $estado !== '')
                    <flux:button variant="ghost" size="sm" wire:click="limpiarFiltros">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar
                    </flux:button>
                @endif
                <span class="text-sm text-zinc-400 whitespace-nowrap">
                    {{ $productos->total() }} producto{{ $productos->total() === 1 ? '' : 's' }}
                </span>
                <flux:dropdown position="bottom" align="end">
                    <flux:button variant="primary" size="sm" icon="arrow-down-tray">Exportar</flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click="exportarExcel" icon="table-cells">Excel</flux:menu.item>
                        <flux:menu.item wire:click="exportarPdf" icon="document-arrow-down">PDF</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>

        {{-- Tabla mejorada --}}
        <div wire:loading.class="opacity-60" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 min-w-[280px]">Producto</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">SKU</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-40">Categoría</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-36">Precio</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Stock</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Estado</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-36">Ventas</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    @php
                        $margen = $producto->precio_costo > 0
                            ? round((($producto->precio_venta - $producto->precio_costo) / $producto->precio_venta) * 100, 1)
                            : null;
                        
                        $estados = [
                            'en_stock' => ['label' => 'En stock', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'],
                            'bajo_stock' => ['label' => 'Bajo stock', 'color' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400'],
                            'agotado' => ['label' => 'Agotado', 'color' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'],
                        ];
                        $e = $estados[$producto->estado] ?? ['label' => $producto->estado, 'color' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400'];
                    @endphp
                    <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                        {{-- Producto --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                         alt="{{ $producto->nombre }}"
                                         class="w-10 h-10 rounded-lg object-cover border border-zinc-200 dark:border-white/10 shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-zinc-100 dark:bg-white/5 flex items-center justify-center border border-zinc-200 dark:border-white/10 shrink-0">
                                        <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-zinc-800 dark:text-white truncate">{{ $producto->nombre }}</p>
                                    @if($producto->descripcion)
                                        <p class="text-xs text-zinc-400 truncate">{{ $producto->descripcion }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- SKU --}}
                        <td class="px-4 py-3 font-mono text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $producto->sku }}
                        </td>

                        {{-- Categoría --}}
                        <td class="px-4 py-3">
                            <div>
                                <p class="text-sm text-zinc-700 dark:text-zinc-200">{{ $producto->categoria->nombre ?? '—' }}</p>
                                @if($producto->subcategoria)
                                    <p class="text-xs text-zinc-400">{{ $producto->subcategoria->nombre }}</p>
                                @endif
                            </div>
                        </td>

                        {{-- Precio --}}
                        <td class="px-4 py-3 text-right">
                            <div>
                                <p class="font-semibold text-zinc-800 dark:text-white">
                                    S/ {{ number_format($producto->precio_venta, 2) }}
                                </p>
                                <div class="flex items-center justify-end gap-1 text-xs text-zinc-400">
                                    @if($producto->precio_costo)
                                        <span>costo S/ {{ number_format($producto->precio_costo, 2) }}</span>
                                    @endif
                                    @if($margen !== null)
                                        <span class="text-zinc-300 dark:text-zinc-600">·</span>
                                        <span class="{{ $margen >= 30 ? 'text-green-600 dark:text-green-400' : ($margen >= 15 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400') }}">
                                            {{ $margen }}% margen
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Stock --}}
                        <td class="px-4 py-3 text-right">
                            <div>
                                <p class="font-medium text-zinc-800 dark:text-white">
                                    {{ $producto->stock }}
                                    <span class="text-xs text-zinc-400 font-normal">/ {{ $producto->stock_minimo }}</span>
                                </p>
                                @if($producto->estado === 'bajo_stock')
                                    <div class="w-full h-1 bg-zinc-200 dark:bg-white/10 rounded-full mt-1.5 overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full transition-all duration-300"
                                             style="width: {{ min(($producto->stock / max($producto->stock_minimo, 1)) * 100, 100) }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Estado --}}
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $e['color'] }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $producto->estado === 'en_stock' ? 'bg-green-500' : ($producto->estado === 'bajo_stock' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                                {{ $e['label'] }}
                            </span>
                        </td>

                        {{-- Ventas --}}
                        <td class="px-4 py-3 text-right">
                            <div>
                                <p class="font-medium text-zinc-800 dark:text-white">
                                    S/ {{ number_format($producto->ingresos_generados ?? 0, 2) }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    {{ number_format($producto->unidades_vendidas ?? 0) }} uds
                                </p>
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <a href="{{ route('productos.show', $producto) }}"
                                   wire:navigate
                                   class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                   title="Ver detalle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button wire:click="editar({{ $producto->id }})"
                                        class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                        title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="eliminar({{ $producto->id }})"
                                        wire:confirm="¿Eliminar este producto? Esta acción no se puede deshacer."
                                        class="p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-zinc-400 hover:text-red-600 dark:hover:text-red-400"
                                        title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-zinc-400">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p>No se encontraron productos con los filtros aplicados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($productos->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-white/10">
                {{ $productos->links() }}
            </div>
        @endif
    </flux:card>
</div>