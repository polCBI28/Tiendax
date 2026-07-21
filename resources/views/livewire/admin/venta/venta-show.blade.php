<div>

    @php
        $estadoColor = match($venta->estado) {
            'completado' => 'green',
            'pendiente' => 'amber',
            'cancelado' => 'red',
            default => 'zinc',
        };
    @endphp

    <div class="mb-6">
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item href="{{ route('ventas.index') }}" wire:navigate>Ventas</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $venta->numero_boleta }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ $venta->numero_boleta }}</flux:heading>
            <flux:badge size="lg" :color="$estadoColor">{{ ucfirst($venta->estado) }}</flux:badge>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info de la venta --}}
        <div class="space-y-6">
            <flux:card class="space-y-4">
                @if($venta->descripcion)
                    <flux:callout icon="document-text" heading="Descripción del pedido" :text="$venta->descripcion" />
                @endif
                <div>
                    <flux:text size="sm" class="text-zinc-400">Fecha</flux:text>
                    <p class="font-medium text-zinc-800 dark:text-white">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <flux:text size="sm" class="text-zinc-400">Registrado por</flux:text>
                    <p class="font-medium text-zinc-800 dark:text-white">{{ $venta->user?->name ?? '—' }}</p>
                </div>

                @php
                    $subtotalVenta = $venta->detalles->sum('subtotal');
                    $montoDescuento = $venta->descuento_valor > 0
                        ? ($venta->descuento_tipo === 'porcentaje'
                            ? round($subtotalVenta * $venta->descuento_valor / 100, 2)
                            : $venta->descuento_valor)
                        : 0;
                    $montoRecargo = $venta->recargo_valor > 0
                        ? ($venta->recargo_tipo === 'porcentaje'
                            ? round(($subtotalVenta - $montoDescuento) * $venta->recargo_valor / 100, 2)
                            : $venta->recargo_valor)
                        : 0;
                    $deuda = $venta->estado === 'completado' ? 0 : max(0, $venta->total - $venta->adelanto);
                @endphp

                <div class="pt-4 border-t border-zinc-200 dark:border-white/10 space-y-2">
                    @if($montoDescuento > 0 || $montoRecargo > 0)
                        <div class="flex justify-between items-center">
                            <flux:text class="text-zinc-500 dark:text-zinc-400">Subtotal</flux:text>
                            <span class="font-mono text-zinc-600 dark:text-zinc-300">S/ {{ number_format($subtotalVenta, 2) }}</span>
                        </div>
                    @endif
                    @if($montoDescuento > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-amber-600 dark:text-amber-400 flex items-center gap-1 text-sm">
                                <flux:icon.tag variant="micro" />
                                Descuento
                                @if($venta->descuento_tipo === 'porcentaje')({{ number_format($venta->descuento_valor, 0) }}%)@endif
                            </span>
                            <span class="font-mono text-amber-600 dark:text-amber-400">- S/ {{ number_format($montoDescuento, 2) }}</span>
                        </div>
                    @endif
                    @if($montoRecargo > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1 text-sm">
                                <flux:icon.plus-circle variant="micro" />
                                Recargo
                                @if($venta->recargo_tipo === 'porcentaje')({{ number_format($venta->recargo_valor, 0) }}%)@endif
                            </span>
                            <span class="font-mono text-blue-600 dark:text-blue-400">+ S/ {{ number_format($montoRecargo, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Total</flux:heading>
                        <flux:heading size="lg" class="font-mono text-blue-600 dark:text-blue-400">S/ {{ number_format($venta->total, 2) }}</flux:heading>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-white/10 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-1 text-sm">
                            <flux:icon.banknotes variant="micro" />
                            Adelanto
                        </span>
                        <span class="font-mono text-zinc-800 dark:text-white">S/ {{ number_format($venta->adelanto, 2) }}</span>
                    </div>
                    @if($deuda > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-red-600 dark:text-red-400 flex items-center gap-1 text-sm">
                                <flux:icon.clock variant="micro" />
                                Resta por cobrar
                            </span>
                            <span class="font-mono font-bold text-red-600 dark:text-red-400">S/ {{ number_format($deuda, 2) }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-1 text-green-600 dark:text-green-400 text-sm">
                            <flux:icon.check-circle variant="micro" />
                            Pagado completamente
                        </div>
                    @endif
                </div>
            </flux:card>

            @if($deuda > 0 && $venta->estado === 'pendiente')
                <flux:button
                    wire:click="completarPago"
                    wire:confirm="¿Confirmar que el cliente completó el pago de S/ {{ number_format($deuda, 2) }}?"
                    variant="primary" icon="check-circle" class="w-full justify-center"
                >
                    Completar Pago (S/ {{ number_format($deuda, 2) }})
                </flux:button>
            @endif

            <flux:button href="{{ route('ventas.index') }}" wire:navigate icon="arrow-left" class="w-full justify-center">
                Volver a Ventas
            </flux:button>
        </div>

        {{-- Detalles --}}
        <flux:card class="lg:col-span-2 overflow-hidden p-0">
            <div class="p-6 border-b border-zinc-200 dark:border-white/10">
                <flux:heading size="lg">Productos Vendidos</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                            <th class="text-left px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Producto</th>
                            <th class="text-center px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Cantidad</th>
                            <th class="text-right px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">P. Unit.</th>
                            <th class="text-right px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                            <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-zinc-800 dark:text-white">{{ $detalle->producto?->nombre ?? '—' }}</p>
                                    @if($detalle->adicional > 0)
                                        @php $precioBase = $detalle->precio_unitario - $detalle->adicional; @endphp
                                        <p class="text-xs text-zinc-400 mt-0.5">
                                            Base S/ {{ number_format($precioBase, 2) }}
                                            <span class="text-amber-600 dark:text-amber-400 font-medium ml-1">+ S/ {{ number_format($detalle->adicional, 2) }} adicional</span>
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-center text-zinc-600 dark:text-zinc-300">{{ $detalle->cantidad }}</td>
                                <td class="px-6 py-4 font-mono text-right text-zinc-600 dark:text-zinc-300">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-right text-zinc-800 dark:text-white">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>

</div>
