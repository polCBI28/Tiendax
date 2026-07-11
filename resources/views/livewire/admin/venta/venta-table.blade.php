<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="p-0 overflow-hidden">
        <div class="flex flex-wrap items-end gap-4 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:select wire:model.live="estado" placeholder="Estado" class="w-48">
                <flux:select.option value="">Todos los estados</flux:select.option>
                <flux:select.option value="borrador">Borrador</flux:select.option>
                <flux:select.option value="pendiente">Pendiente</flux:select.option>
                <flux:select.option value="completado">Completado</flux:select.option>
                <flux:select.option value="cancelado">Cancelado</flux:select.option>
            </flux:select>
            <flux:spacer />
            <flux:text size="sm" class="text-zinc-400 whitespace-nowrap">{{ $ventas->total() }} venta{{ $ventas->total() === 1 ? '' : 's' }}</flux:text>
        </div>

        <div wire:loading.class="opacity-60">
        <flux:table :paginate="$ventas" pagination:class="px-4 pb-4">
            <flux:table.columns>
                <flux:table.column class="w-[16%]">Boleta</flux:table.column>
                <flux:table.column class="w-[20%]">Cliente</flux:table.column>
                <flux:table.column class="w-[12%]">Fecha</flux:table.column>
                <flux:table.column align="end" class="w-[12%]">Total</flux:table.column>
                <flux:table.column align="end" class="w-[12%]">Deuda</flux:table.column>
                <flux:table.column align="center" class="w-[12%]">Estado</flux:table.column>
                <flux:table.column align="center" class="w-[16%]">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($ventas as $venta)
                @php
                    $deuda = $venta->estado === 'completado' ? 0 : max(0, $venta->total - $venta->adelanto);
                    $estadoColor = match($venta->estado) {
                        'completado' => 'green',
                        'pendiente' => 'amber',
                        'cancelado' => 'red',
                        default => 'zinc',
                    };
                @endphp
                <flux:table.row wire:key="venta-{{ $venta->id }}">
                    <flux:table.cell variant="strong">{{ $venta->numero_boleta }}</flux:table.cell>
                    <flux:table.cell>{{ $venta->cliente->nombre ?? 'Sin cliente' }}</flux:table.cell>
                    <flux:table.cell>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</flux:table.cell>
                    <flux:table.cell align="end" variant="strong">S/ {{ number_format($venta->total, 2) }}</flux:table.cell>
                    <flux:table.cell align="end">
                        @if($deuda > 0)
                            <span class="text-red-600 dark:text-red-400">S/ {{ number_format($deuda, 2) }}</span>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge size="sm" :color="$estadoColor">{{ ucfirst($venta->estado) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <flux:button href="{{ route('ventas.show', $venta) }}" icon="eye" variant="ghost" size="sm" tooltip="Ver detalle" />

                            <flux:dropdown position="bottom" align="end">
                                <flux:button icon="ellipsis-vertical" variant="ghost" size="sm" tooltip="Cambiar estado" />
                                <flux:menu>
                                    <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'borrador')">Marcar Borrador</flux:menu.item>
                                    <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'pendiente')">Marcar Pendiente</flux:menu.item>
                                    <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'completado')">Marcar Completado</flux:menu.item>
                                    <flux:menu.item wire:click="cambiarEstado({{ $venta->id }}, 'cancelado')" variant="danger">Marcar Cancelado</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>

                            @if($venta->estado === 'pendiente' && $deuda > 0)
                                <flux:button
                                    wire:click="completarPago({{ $venta->id }})"
                                    wire:confirm="¿Marcar esta venta como pagada por completo?"
                                    icon="check" variant="ghost" size="sm" tooltip="Completar pago"
                                />
                            @endif

                            <flux:button
                                wire:click="eliminar({{ $venta->id }})"
                                wire:confirm="¿Eliminar esta venta? Esta acción no se puede deshacer."
                                icon="trash" variant="ghost" size="sm" tooltip="Eliminar"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <flux:icon.shopping-bag class="size-12" />
                            <flux:text>No se encontraron ventas con los filtros aplicados.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </flux:card>

</div>
