<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="p-0 overflow-hidden">
        <div class="flex flex-wrap items-end gap-4 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:input
                wire:model.live.debounce.400ms="search"
                icon="magnifying-glass"
                placeholder="Buscar por producto o SKU..."
                class="flex-1 min-w-[220px]"
            />
            <flux:spacer />
            <flux:text size="sm" class="text-zinc-400 whitespace-nowrap">{{ $detalleVentas->total() }} línea{{ $detalleVentas->total() === 1 ? '' : 's' }}</flux:text>
        </div>

        <div wire:loading.class="opacity-60">
        <flux:table :paginate="$detalleVentas" pagination:class="px-4 pb-4">
            <flux:table.columns>
                <flux:table.column class="w-[20%]">Boleta</flux:table.column>
                <flux:table.column class="w-[30%]">Producto</flux:table.column>
                <flux:table.column align="end" class="w-[12%]">Cantidad</flux:table.column>
                <flux:table.column align="end" class="w-[14%]">P. Unitario</flux:table.column>
                <flux:table.column align="end" class="w-[14%]">Subtotal</flux:table.column>
                <flux:table.column align="center" class="w-[10%]">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($detalleVentas as $detalle)
                <flux:table.row wire:key="detalle-{{ $detalle->id }}">
                    <flux:table.cell variant="strong">{{ $detalle->venta->numero_boleta ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        <p class="font-medium text-zinc-800 dark:text-white">{{ $detalle->producto->nombre ?? '—' }}</p>
                        <p class="text-xs text-zinc-400">{{ $detalle->producto->sku ?? '' }}</p>
                    </flux:table.cell>
                    <flux:table.cell align="end">{{ $detalle->cantidad }}</flux:table.cell>
                    <flux:table.cell align="end">S/ {{ number_format($detalle->precio_unitario, 2) }}</flux:table.cell>
                    <flux:table.cell align="end" variant="strong" class="text-emerald-600 dark:text-emerald-400">S/ {{ number_format($detalle->subtotal, 2) }}</flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <flux:button wire:click="editar({{ $detalle->id }})" icon="pencil" variant="ghost" size="sm" tooltip="Editar" />
                            <flux:button
                                wire:click="eliminar({{ $detalle->id }})"
                                wire:confirm="¿Eliminar esta línea? Esta acción no se puede deshacer."
                                icon="trash" variant="ghost" size="sm" tooltip="Eliminar"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <flux:icon.receipt-percent class="size-12" />
                            <flux:text>No se encontraron líneas con los filtros aplicados.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </flux:card>

</div>
