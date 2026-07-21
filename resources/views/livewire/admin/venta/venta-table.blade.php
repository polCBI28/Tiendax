<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="overflow-hidden p-0">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:select wire:model.live="estado" placeholder="Estado" class="w-44">
                <flux:select.option value="">Todos los estados</flux:select.option>
                <flux:select.option value="borrador">Borrador</flux:select.option>
                <flux:select.option value="pendiente">Pendiente</flux:select.option>
                <flux:select.option value="completado">Completado</flux:select.option>
                <flux:select.option value="cancelado">Cancelado</flux:select.option>
            </flux:select>
            <flux:input wire:model.live="desde" type="date" label="Desde" class="w-40" />
            <flux:input wire:model.live="hasta" type="date" label="Hasta" class="w-40" />

            <div class="flex items-center gap-2 ml-auto">
                @if($estado !== '' || $desde !== '' || $hasta !== '')
                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="limpiarFiltros">Limpiar</flux:button>
                @endif
                <span class="text-sm text-zinc-400 whitespace-nowrap">
                    {{ $ventas->total() }} venta{{ $ventas->total() === 1 ? '' : 's' }}
                </span>
                <flux:button wire:click="exportarPdf" variant="primary" size="sm" icon="document-arrow-down">
                    Generar PDF
                </flux:button>
            </div>
        </div>

        {{-- Tabla --}}
        <div wire:loading.class="opacity-60" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-40">Boleta</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 min-w-[160px]">Cliente</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Fecha</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Total</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Deuda</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Estado</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-40">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                    @php
                        $deuda = $venta->estado === 'completado' ? 0 : max(0, $venta->total - $venta->adelanto);
                        $estadoColor = match($venta->estado) {
                            'completado' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'pendiente' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                            'cancelado' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            default => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400',
                        };
                        $estadoDot = match($venta->estado) {
                            'completado' => 'bg-green-500',
                            'pendiente' => 'bg-amber-500',
                            'cancelado' => 'bg-red-500',
                            default => 'bg-zinc-400',
                        };
                    @endphp
                    <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="px-4 py-3 font-mono text-sm font-medium text-zinc-800 dark:text-white">{{ $venta->numero_boleta }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $venta->cliente->nombre ?? 'Sin cliente' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-zinc-800 dark:text-white">S/ {{ number_format($venta->total, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($deuda > 0)
                                <span class="text-red-600 dark:text-red-400 font-medium">S/ {{ number_format($deuda, 2) }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoColor }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $estadoDot }}"></span>
                                {{ ucfirst($venta->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <a href="{{ route('ventas.show', $venta) }}"
                                   wire:navigate
                                   class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                   title="Ver detalle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                <flux:dropdown position="bottom" align="end">
                                    <button class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" title="Cambiar estado">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="1.5" />
                                            <circle cx="12" cy="12" r="1.5" />
                                            <circle cx="12" cy="19" r="1.5" />
                                        </svg>
                                    </button>
                                    <flux:menu>
                                        <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'borrador')">Marcar Borrador</flux:menu.item>
                                        <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'pendiente')">Marcar Pendiente</flux:menu.item>
                                        <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'completado')">Marcar Completado</flux:menu.item>
                                        <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'cancelado')" variant="danger">Marcar Cancelado</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>

                                @if($venta->estado === 'pendiente' && $deuda > 0)
                                    <button wire:click="completarPago({{ $venta->id }})"
                                            wire:confirm="¿Marcar esta venta como pagada por completo?"
                                            class="p-1.5 rounded hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors text-zinc-400 hover:text-green-600 dark:hover:text-green-400"
                                            title="Completar pago">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endif

                                <button wire:click="eliminar({{ $venta->id }})"
                                        wire:confirm="¿Eliminar esta venta? Esta acción no se puede deshacer."
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
                        <td colspan="7" class="px-4 py-12 text-center text-zinc-400">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <p>No se encontraron ventas con los filtros aplicados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ventas->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-white/10">
                {{ $ventas->links() }}
            </div>
        @endif
    </flux:card>

</div>
