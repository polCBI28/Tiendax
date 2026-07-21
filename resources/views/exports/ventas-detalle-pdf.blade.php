<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { size: landscape; }
        body { font-family: sans-serif; font-size: 10px; color: #27272a; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #71717a; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px 7px; border-bottom: 1px solid #e4e4e7; text-align: left; }
        th { background: #f4f4f5; font-weight: bold; text-transform: uppercase; font-size: 8px; color: #52525b; }
        .text-right { text-align: right; }
        .totales { margin-top: 16px; font-size: 11px; }
    </style>
</head>
<body>
    <h1>Detalle de Ventas</h1>
    <p class="meta">
        Generado el {{ now()->format('d/m/Y H:i') }}
        · {{ $ventas->count() }} ventas
        @if($desde || $hasta) · {{ $desde ?: '…' }} a {{ $hasta ?: '…' }} @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>Boleta</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th class="text-right">Uds.</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Descuento</th>
                <th class="text-right">Recargo</th>
                <th class="text-right">Total</th>
                <th class="text-right">Adelanto</th>
                <th class="text-right">Deuda</th>
                <th>Estado</th>
                <th>Vendedor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                @php
                    $subtotalVenta = $venta->detalles->sum('subtotal');
                    $montoDescuento = $venta->descuento_valor > 0
                        ? ($venta->descuento_tipo === 'porcentaje' ? round($subtotalVenta * $venta->descuento_valor / 100, 2) : $venta->descuento_valor)
                        : 0;
                    $montoRecargo = $venta->recargo_valor > 0
                        ? ($venta->recargo_tipo === 'porcentaje' ? round(($subtotalVenta - $montoDescuento) * $venta->recargo_valor / 100, 2) : $venta->recargo_valor)
                        : 0;
                    $deuda = $venta->estado === 'completado' ? 0 : max(0, $venta->total - $venta->adelanto);
                @endphp
                <tr>
                    <td>{{ $venta->numero_boleta }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
                    <td>{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</td>
                    <td class="text-right">{{ $venta->detalles->sum('cantidad') }}</td>
                    <td class="text-right">S/ {{ number_format($subtotalVenta, 2) }}</td>
                    <td class="text-right">{{ $montoDescuento > 0 ? '- S/ '.number_format($montoDescuento, 2) : '—' }}</td>
                    <td class="text-right">{{ $montoRecargo > 0 ? '+ S/ '.number_format($montoRecargo, 2) : '—' }}</td>
                    <td class="text-right">S/ {{ number_format($venta->total, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($venta->adelanto, 2) }}</td>
                    <td class="text-right">{{ $deuda > 0 ? 'S/ '.number_format($deuda, 2) : 'Pagado' }}</td>
                    <td>{{ ucfirst($venta->estado) }}</td>
                    <td>{{ $venta->user?->name ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="totales"><strong>Total: S/ {{ number_format($ventas->sum('total'), 2) }}</strong></p>
</body>
</html>
