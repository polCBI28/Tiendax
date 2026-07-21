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
                placeholder="Buscar por producto o SKU..."
                class="flex-1 min-w-[220px]"
            />

            <div class="flex items-center gap-2 ml-auto">
                <span class="text-sm text-zinc-400 whitespace-nowrap">
                    {{ $detalleVentas->total() }} línea{{ $detalleVentas->total() === 1 ? '' : 's' }}
                </span>
            </div>
        </div>

        {{-- Tabla --}}
        <div wire:loading.class="opacity-60" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-40">Boleta</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 min-w-[200px]">Producto</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Cantidad</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-36">P. Unitario</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-36">Subtotal</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detalleVentas as $detalle)
                    <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="px-4 py-3 font-mono text-sm font-medium text-zinc-800 dark:text-white">{{ $detalle->venta->numero_boleta ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-zinc-800 dark:text-white">{{ $detalle->producto->nombre ?? '—' }}</p>
                            <p class="text-xs text-zinc-400">{{ $detalle->producto->sku ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ $detalle->cantidad }}</td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <button wire:click="editar({{ $detalle->id }})"
                                        class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                        title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="eliminar({{ $detalle->id }})"
                                        wire:confirm="¿Eliminar esta línea? Esta acción no se puede deshacer."
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
                        <td colspan="6" class="px-4 py-12 text-center text-zinc-400">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                </svg>
                                <p>No se encontraron líneas con los filtros aplicados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($detalleVentas->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-white/10">
                {{ $detalleVentas->links() }}
            </div>
        @endif
    </flux:card>

</div>
