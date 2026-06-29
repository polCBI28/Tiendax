<x-layouts.app title="Detalle de Venta">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('ventas.index') }}" class="hover:text-primary transition-colors">Ventas</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $venta->numero_boleta }}</span>
    </nav>
    <div class="flex items-center justify-between">
        <h2 class="font-headline-lg text-headline-lg text-on-surface">{{ $venta->numero_boleta }}</h2>
        @if($venta->estado === 'completado')
            <span class="px-3 py-1 bg-green-50 text-green-700 font-label-lg rounded-full border border-green-200">Completado</span>
        @elseif($venta->estado === 'pendiente')
            <span class="px-3 py-1 bg-primary-container/20 text-primary font-label-lg rounded-full border border-primary/20">Pendiente</span>
        @elseif($venta->estado === 'borrador')
            <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant font-label-lg rounded-full border border-outline-variant">Borrador</span>
        @else
            <span class="px-3 py-1 bg-error-container/20 text-error font-label-lg rounded-full border border-error/20">Cancelado</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info de la venta --}}
    <div class="space-y-6">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 space-y-4">
            @if($venta->descripcion)
            <div class="p-3 bg-primary/5 rounded-xl border border-primary/15 flex gap-3">
                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">description</span>
                <div>
                    <p class="font-label-sm text-primary mb-0.5">Descripción del pedido</p>
                    <p class="font-body-sm text-on-surface">{{ $venta->descripcion }}</p>
                </div>
            </div>
            @endif
            <div>
                <p class="font-label-sm text-on-surface-variant mb-1">Fecha</p>
                <p class="font-label-lg text-on-surface">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="font-label-sm text-on-surface-variant mb-1">Registrado por</p>
                <p class="font-label-lg text-on-surface">{{ $venta->user?->name ?? '—' }}</p>
            </div>
            <div class="pt-4 border-t border-outline-variant space-y-2">
                @php
                    $subtotalVenta  = $venta->detalles->sum('subtotal');
                    $montoDescuento = $venta->descuento_valor > 0
                        ? ($venta->descuento_tipo === 'porcentaje'
                            ? round($subtotalVenta * $venta->descuento_valor / 100, 2)
                            : $venta->descuento_valor)
                        : 0;
                    $montoRecargo   = $venta->recargo_valor > 0
                        ? ($venta->recargo_tipo === 'porcentaje'
                            ? round(($subtotalVenta - $montoDescuento) * $venta->recargo_valor / 100, 2)
                            : $venta->recargo_valor)
                        : 0;
                @endphp
                @if($montoDescuento > 0 || $montoRecargo > 0)
                <div class="flex justify-between items-center">
                    <span class="font-label-lg text-on-surface-variant">Subtotal</span>
                    <span class="font-label-lg text-on-surface-variant font-mono-data">S/ {{ number_format($subtotalVenta, 2) }}</span>
                </div>
                @endif
                @if($montoDescuento > 0)
                <div class="flex justify-between items-center">
                    <span class="font-label-lg text-secondary flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">discount</span>
                        Descuento
                        @if($venta->descuento_tipo === 'porcentaje')
                            ({{ number_format($venta->descuento_valor, 0) }}%)
                        @endif
                    </span>
                    <span class="font-label-lg text-secondary font-mono-data">- S/ {{ number_format($montoDescuento, 2) }}</span>
                </div>
                @endif
                @if($montoRecargo > 0)
                <div class="flex justify-between items-center">
                    <span class="font-label-lg text-tertiary flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                        Recargo
                        @if($venta->recargo_tipo === 'porcentaje')
                            ({{ number_format($venta->recargo_valor, 0) }}%)
                        @endif
                    </span>
                    <span class="font-label-lg text-tertiary font-mono-data">+ S/ {{ number_format($montoRecargo, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="font-headline-md text-on-surface">Total</span>
                    <span class="font-headline-md text-primary font-mono-data">S/ {{ number_format($venta->total, 2) }}</span>
                </div>
            </div>

            {{-- Info de Pagos --}}
            @php
                $deuda = $venta->estado === 'completado' ? 0 : max(0, $venta->total - $venta->adelanto);
            @endphp
            <div class="pt-4 border-t border-outline-variant space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-label-lg text-on-surface-variant flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">payments</span>
                        Adelanto
                    </span>
                    <span class="font-label-lg text-on-surface font-mono-data">S/ {{ number_format($venta->adelanto, 2) }}</span>
                </div>
                @if($deuda > 0)
                <div class="flex justify-between items-center">
                    <span class="font-label-lg text-error flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">pending</span>
                        Resta por cobrar
                    </span>
                    <span class="font-label-lg text-error font-mono-data font-bold">S/ {{ number_format($deuda, 2) }}</span>
                </div>
                @else
                <div class="flex items-center gap-1 text-green-600 font-label-lg">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Pagado completamente
                </div>
                @endif
            </div>
        </div>

        @if($deuda > 0 && $venta->estado === 'pendiente')
        <form action="{{ route('ventas.completar-pago', $venta) }}" method="POST"
              onsubmit="return confirm('¿Confirmar que el cliente completó el pago de S/ {{ number_format($deuda, 2) }}?')">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="flex items-center justify-center gap-2 w-full py-3 bg-primary text-on-primary rounded-xl font-label-lg hover:opacity-90 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Completar Pago (S/ {{ number_format($deuda, 2) }})
            </button>
        </form>
        @endif

        <a href="{{ route('ventas.index') }}"
           class="flex items-center justify-center gap-2 w-full py-3 bg-surface-container-high text-on-surface rounded-xl font-label-lg hover:bg-outline-variant/20 transition-all">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Volver a Ventas
        </a>
    </div>

    {{-- Detalles --}}
    <div class="lg:col-span-2">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-6 border-b border-outline-variant">
                <h3 class="font-headline-md text-headline-md text-on-surface">Productos Vendidos</h3>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-container border-b border-outline-variant">
                        <th class="px-6 py-3 font-label-lg text-on-surface text-left">Producto</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-center">Cantidad</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-right">P. Unit.</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach($venta->detalles as $detalle)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-body-sm text-on-surface">{{ $detalle->producto?->nombre ?? '—' }}</p>
                            @if($detalle->adicional > 0)
                            @php $precioBase = $detalle->precio_unitario - $detalle->adicional; @endphp
                            <p class="font-label-sm text-on-surface-variant mt-0.5">
                                Base S/ {{ number_format($precioBase, 2) }}
                                <span class="text-secondary font-medium ml-1">+ S/ {{ number_format($detalle->adicional, 2) }} adicional</span>
                            </p>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono-data text-on-surface text-center">{{ $detalle->cantidad }}</td>
                        <td class="px-6 py-4 font-mono-data text-on-surface text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="px-6 py-4 font-mono-data font-bold text-on-surface text-right">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

</x-layouts.app>
