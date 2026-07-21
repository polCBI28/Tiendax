@extends('exports.pdf-base')

@section('titulo', 'Listado de Subcategorías')
@section('subtitulo', '· '.$subcategorias->count().' subcategorías')

@section('content')
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Descripción</th>
            <th class="text-right">Productos</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subcategorias as $sub)
        <tr>
            <td>{{ $sub->nombre }}</td>
            <td>{{ $sub->categoria?->nombre ?? '—' }}</td>
            <td>{{ $sub->descripcion ?? '—' }}</td>
            <td class="text-right">{{ $sub->productos_count }}</td>
            <td>{{ $sub->activo ? 'Activo' : 'Inactivo' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
