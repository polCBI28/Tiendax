<div>
    <flux:dropdown position="bottom" align="start">
        <button class="relative flex items-center justify-center size-9 rounded-lg text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors" title="Notificaciones">
            <flux:icon.bell variant="outline" class="size-5" />
            @if($total > 0)
                <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full">
                    {{ min($total, 99) }}
                </span>
            @endif
        </button>

        <flux:menu class="w-[340px]">
            <div class="px-3 py-2.5 border-b border-zinc-200 dark:border-white/10">
                <p class="text-sm font-semibold text-zinc-800 dark:text-white">Notificaciones</p>
                <p class="text-xs text-zinc-400">
                    {{ $total }} {{ $total === 1 ? 'alerta activa' : 'alertas activas' }}
                </p>
            </div>

            <div class="max-h-80 overflow-y-auto">
                @if($totalAgotados > 0)
                    <div class="px-3 pt-3 pb-1 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-red-600 dark:text-red-400">Productos agotados</p>
                        <flux:badge size="sm" color="red">{{ $totalAgotados }}</flux:badge>
                    </div>
                    @foreach($agotados as $producto)
                        <flux:menu.item as="a" href="{{ route('productos.show', $producto) }}" wire:navigate class="flex items-start gap-3 px-3 py-2">
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <flux:icon.x-circle variant="mini" class="text-red-600 dark:text-red-400" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-zinc-800 dark:text-white truncate">{{ $producto->nombre }}</p>
                                <p class="text-xs text-zinc-400">Sin stock disponible</p>
                            </div>
                        </flux:menu.item>
                    @endforeach
                @endif

                @if($totalBajoStock > 0)
                    <div class="px-3 pt-3 pb-1 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Stock bajo</p>
                        <flux:badge size="sm" color="amber">{{ $totalBajoStock }}</flux:badge>
                    </div>
                    @foreach($bajoStock as $producto)
                        <flux:menu.item as="a" href="{{ route('productos.show', $producto) }}" wire:navigate class="flex items-start gap-3 px-3 py-2">
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                <flux:icon.archive-box variant="mini" class="text-amber-600 dark:text-amber-400" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-zinc-800 dark:text-white truncate">{{ $producto->nombre }}</p>
                                <p class="text-xs text-zinc-400">
                                    Stock: <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $producto->stock }}</span> / Mín: {{ $producto->stock_minimo }}
                                </p>
                            </div>
                        </flux:menu.item>
                    @endforeach
                @endif

                @if($totalVentasPendientes > 0)
                    <div class="px-3 pt-3 pb-1 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Ventas pendientes de cobro</p>
                        <flux:badge size="sm" color="blue">{{ $totalVentasPendientes }}</flux:badge>
                    </div>
                    @foreach($ventasPendientes as $venta)
                        <flux:menu.item as="a" href="{{ route('ventas.show', $venta) }}" wire:navigate class="flex items-start gap-3 px-3 py-2">
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <flux:icon.banknotes variant="mini" class="text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-zinc-800 dark:text-white truncate">{{ $venta->numero_boleta }}</p>
                                <p class="text-xs text-zinc-400">
                                    {{ $venta->cliente?->nombre ?? 'Sin cliente' }} · Debe S/ {{ number_format($venta->total - $venta->adelanto, 2) }}
                                </p>
                            </div>
                        </flux:menu.item>
                    @endforeach
                @endif

                @if($total === 0)
                    <div class="flex flex-col items-center gap-2 py-8 text-zinc-400">
                        <flux:icon.check-circle class="size-8 text-green-400" />
                        <p class="text-sm">Todo en orden, sin alertas pendientes.</p>
                    </div>
                @endif
            </div>
        </flux:menu>
    </flux:dropdown>
</div>
