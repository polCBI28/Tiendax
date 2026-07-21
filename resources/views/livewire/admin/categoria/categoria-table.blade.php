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
                placeholder="Buscar categoría..."
                class="flex-1 min-w-[200px]"
            />

            <flux:select wire:model.live="estado" placeholder="Estado" class="w-40">
                <flux:select.option value="">Todos los estados</flux:select.option>
                <flux:select.option value="activo">Activo</flux:select.option>
                <flux:select.option value="inactivo">Inactivo</flux:select.option>
            </flux:select>

            <div class="flex items-center gap-2 ml-auto">
                @if($search !== '' || $estado !== '')
                    <flux:button variant="ghost" size="sm" wire:click="limpiarFiltros">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar
                    </flux:button>
                @endif
                <span class="text-sm text-zinc-400 whitespace-nowrap">
                    {{ $categorias->total() }} categoría{{ $categorias->total() === 1 ? '' : 's' }}
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

        {{-- Tabla --}}
        <div wire:loading.class="opacity-60" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 min-w-[240px]">
                            <button wire:click="sort('nombre')" class="inline-flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">
                                Categoría
                                @if($ordenar === 'nombre')
                                    <span class="text-zinc-400">{{ $dir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Descripción</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Productos</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Estado</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                    <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                        {{-- Categoría --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">{{ $categoria->icono ?? 'category' }}</span>
                                </div>
                                <p class="font-medium text-zinc-800 dark:text-white">{{ $categoria->nombre }}</p>
                            </div>
                        </td>

                        {{-- Descripción --}}
                        <td class="px-4 py-3">
                            <span class="text-zinc-500 dark:text-zinc-400 truncate block max-w-md">{{ $categoria->descripcion ?? '—' }}</span>
                        </td>

                        {{-- Productos --}}
                        <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">
                            {{ $categoria->productos_count }}
                        </td>

                        {{-- Estado --}}
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $categoria->activo ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $categoria->activo ? 'bg-green-500' : 'bg-zinc-400' }}"></span>
                                {{ $categoria->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <a href="{{ route('categorias.show', $categoria) }}"
                                   wire:navigate
                                   class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                   title="Ver detalle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button wire:click="editar({{ $categoria->id }})"
                                        class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                        title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="eliminar({{ $categoria->id }})"
                                        wire:confirm="¿Eliminar esta categoría? Esta acción no se puede deshacer."
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
                        <td colspan="5" class="px-4 py-12 text-center text-zinc-400">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <p>No se encontraron categorías con los filtros aplicados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($categorias->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-white/10">
                {{ $categorias->links() }}
            </div>
        @endif
    </flux:card>

</div>
