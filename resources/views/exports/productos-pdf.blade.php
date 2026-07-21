@extends('exports.pdf-base')

@section('titulo', 'Listado de Productos')
@section('subtitulo', '· '.$productos->count().' productos')

@section('content')
<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Subcategoría</th>
            <th class="text-right">Precio</th>
            <th class="text-right">Stock</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($productos as $producto)
        <tr>
            <td>{{ $producto->sku }}</td>
            <td>{{ $producto->nombre }}</td>
            <td>{{ $producto->categoria?->nombre ?? '—' }}</td>
            <td>{{ $producto->subcategoria?->nombre ?? '—' }}</td>
            <td class="text-right">S/ {{ number_format($producto->precio_venta, 2) }}</td>
            <td class="text-right">{{ $producto->stock }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $producto->estado)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
