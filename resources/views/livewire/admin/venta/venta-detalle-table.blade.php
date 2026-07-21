<div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <flux:card>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                    <flux:icon.receipt-percent variant="solid" class="size-4 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:text size="sm" class="text-zinc-400">Total ventas</flux:text>
            </div>
            <flux:heading size="lg">{{ number_format($estadisticas['total_ventas']) }}</flux:heading>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <flux:icon.banknotes variant="solid" class="size-4 text-emerald-600 dark:text-emerald-400" />
                </div>
                <flux:text size="sm" class="text-zinc-400">Ingresos</flux:text>
            </div>
            <flux:heading size="lg">S/ {{ number_format($estadisticas['ingresos'], 2) }}</flux:heading>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <flux:icon.tag variant="solid" class="size-4 text-amber-600 dark:text-amber-400" />
                </div>
                <flux:text size="sm" class="text-zinc-400">Descuentos</flux:text>
            </div>
            <flux:heading size="lg" class="text-amber-600 dark:text-amber-400">S/ {{ number_format($estadisticas['descuentos'], 2) }}</flux:heading>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                    <flux:icon.chart-bar variant="solid" class="size-4 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:text size="sm" class="text-zinc-400">Ticket promedio</flux:text>
            </div>
            <flux:heading size="lg">S/ {{ number_format($estadisticas['ticket_promedio'], 2) }}</flux:heading>
        </flux:card>
    </div>

    <flux:card class="overflow-hidden p-0">
        {{-- Filtros --}}
        <div class="flex flex-wrap items-end gap-3 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" placeholder="Boleta o cliente..." class="flex-1 min-w-[200px]" />
            <flux:input wire:model.live="desde" type="date" label="Desde" class="w-40" />
            <flux:input wire:model.live="hasta" type="date" label="Hasta" class="w-40" />
            <flux:select wire:model.live="clienteId" placeholder="Cliente" class="w-44">
                <flux:select.option value="">Todos</flux:select.option>
                @foreach($clientes as $cli)
                    <flux:select.option value="{{ $cli->id }}">{{ $cli->nombre }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="estado" placeholder="Estado" class="w-36">
                <flux:select.option value="">Todos</flux:select.option>
                <flux:select.option value="completado">Completado</flux:select.option>
                <flux:select.option value="pendiente">Pendiente</flux:select.option>
                <flux:select.option value="borrador">Borrador</flux:select.option>
                <flux:select.option value="cancelado">Cancelado</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="descuento" placeholder="Descuento" class="w-36">
                <flux:select.option value="">Todos</flux:select.option>
                <flux:select.option value="con">Con descuento</flux:select.option>
                <flux:select.option value="sin">Sin descuento</flux:select.option>
            </flux:select>

            <div class="flex items-center gap-2 ml-auto">
                @if($search !== '' || $desde !== '' || $hasta !== '' || $clienteId !== '' || $estado !== '' || $descuento !== '')
                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="limpiarFiltros">Limpiar</flux:button>
                @endif
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
        <div class="px-4" wire:loading.class="opacity-60">
        <flux:table :paginate="$ventas" pagination:class="pb-4">
            <flux:table.columns>
                <flux:table.column>Boleta</flux:table.column>
                <flux:table.column class="w-32">Fecha</flux:table.column>
                <flux:table.column>Cliente</flux:table.column>
                <flux:table.column>Productos</flux:table.column>
                <flux:table.column align="center" class="w-16">Uds.</flux:table.column>
                <flux:table.column align="end" class="w-28">Subtotal</flux:table.column>
                <flux:table.column align="end" class="w-28">Descuento</flux:table.column>
                <flux:table.column align="end" class="w-28">Recargo</flux:table.column>
                <flux:table.column align="end" class="w-28">Total</flux:table.column>
                <flux:table.column align="end" class="w-28">Adelanto</flux:table.column>
                <flux:table.column align="end" class="w-28">Deuda</flux:table.column>
                <flux:table.column align="center" class="w-28">Estado</flux:table.column>
                <flux:table.column class="w-32">Vendedor</flux:table.column>
                <flux:table.column align="center" class="w-16">Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($ventas as $venta)
                    @php
                        $subtotalVenta = $venta->detalles->sum('subtotal');
                        $totalUnidades = $venta->detalles->sum('cantidad');
                        $montoDescuento = $subtotalVenta - $venta->total > 0 && $venta->descuento_valor > 0
                            ? ($venta->descuento_tipo === 'porcentaje' ? round($subtotalVenta * $venta->descuento_valor / 100, 2) : $venta->descuento_valor)
                            : 0;
                        $montoRecargo = $venta->recargo_valor > 0
                            ? ($venta->recargo_tipo === 'porcentaje' ? round(($subtotalVenta - $montoDescuento) * $venta->recargo_valor / 100, 2) : $venta->recargo_valor)
                            : 0;
                        $deudaVenta = $venta->estado === 'completado' ? 0 : max(0, $venta->total - $venta->adelanto);
                        $estadoColor = match($venta->estado) {
                            'completado' => 'green',
                            'pendiente' => 'amber',
                            'cancelado' => 'red',
                            default => 'zinc',
                        };
                    @endphp
                    <flux:table.row wire:key="vd-{{ $venta->id }}">
                        <flux:table.cell variant="strong">
                            <a href="{{ route('ventas.show', $venta) }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400">{{ $venta->numero_boleta }}</a>
                        </flux:table.cell>
                        <flux:table.cell>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1 max-w-[240px]">
                                @foreach($venta->detalles->take(2) as $det)
                                    <flux:badge size="sm" :color="$loop->first ? 'blue' : 'zinc'">{{ \Illuminate\Support\Str::limit($det->producto?->nombre ?? '—', 16) }} x{{ $det->cantidad }}</flux:badge>
                                @endforeach
                                @if($venta->detalles->count() > 2)
                                    <flux:badge size="sm" color="zinc">+{{ $venta->detalles->count() - 2 }}</flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="center">{{ $totalUnidades }}</flux:table.cell>
                        <flux:table.cell align="end">S/ {{ number_format($subtotalVenta, 2) }}</flux:table.cell>
                        <flux:table.cell align="end">
                            @if($montoDescuento > 0)
                                <span class="text-amber-600 dark:text-amber-400">- S/ {{ number_format($montoDescuento, 2) }}</span>
                            @else <span class="text-zinc-400">—</span> @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            @if($montoRecargo > 0)
                                <span class="text-blue-600 dark:text-blue-400">+ S/ {{ number_format($montoRecargo, 2) }}</span>
                            @else <span class="text-zinc-400">—</span> @endif
                        </flux:table.cell>
                        <flux:table.cell align="end" variant="strong">S/ {{ number_format($venta->total, 2) }}</flux:table.cell>
                        <flux:table.cell align="end">S/ {{ number_format($venta->adelanto, 2) }}</flux:table.cell>
                        <flux:table.cell align="end">
                            @if($deudaVenta > 0)
                                <span class="text-red-600 dark:text-red-400 font-medium">S/ {{ number_format($deudaVenta, 2) }}</span>
                            @else <span class="text-emerald-600 dark:text-emerald-400">Pagado</span> @endif
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <flux:badge size="sm" :color="$estadoColor">{{ ucfirst($venta->estado) }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $venta->user?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell align="center">
                            <flux:button href="{{ route('ventas.show', $venta) }}" wire:navigate icon="eye" variant="ghost" size="sm" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="14">
                            <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                                <flux:icon.receipt-percent class="size-12" />
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
