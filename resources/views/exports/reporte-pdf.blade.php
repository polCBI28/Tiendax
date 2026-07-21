@extends('exports.pdf-base')

@section('titulo', 'Reporte de Ventas')
@section('subtitulo', '· '.$desde.' a '.$hasta)

@section('content')
<table style="margin-bottom: 20px;">
    <thead>
        <tr>
            <th>Ventas</th>
            <th>Ingresos</th>
            <th>Ticket Promedio</th>
            <th>Unidades Vendidas</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ number_format($cantidad) }}</td>
            <td>S/ {{ number_format($ingresos, 2) }}</td>
            <td>S/ {{ number_format($ticketPromedio, 2) }}</td>
            <td>{{ number_format($unidades) }}</td>
        </tr>
    </tbody>
</table>

<p style="font-weight: bold; margin-bottom: 6px;">Top 10 Productos Más Vendidos</p>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Producto</th>
            <th class="text-right">Unidades</th>
            <th class="text-right">Ingresos</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topProductos as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->producto?->nombre ?? '—' }}</td>
            <td class="text-right">{{ number_format($item->total_vendido) }}</td>
            <td class="text-right">S/ {{ number_format($item->total_ingresos, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4">Sin datos de ventas en el rango seleccionado.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
