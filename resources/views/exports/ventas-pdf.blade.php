@extends('exports.pdf-base')

@section('titulo', 'Listado de Ventas')
@section('subtitulo', '· '.$ventas->count().' ventas'.($desde || $hasta ? ' · '.($desde ?: '…').' a '.($hasta ?: '…') : ''))

@section('content')
<table>
    <thead>
        <tr>
            <th>Boleta</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th class="text-right">Total</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ventas as $venta)
        <tr>
            <td>{{ $venta->numero_boleta }}</td>
            <td>{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
            <td class="text-right">S/ {{ number_format($venta->total, 2) }}</td>
            <td>{{ ucfirst($venta->estado) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<p class="totales"><strong>Total: S/ {{ number_format($ventas->sum('total'), 2) }}</strong></p>
@endsection
