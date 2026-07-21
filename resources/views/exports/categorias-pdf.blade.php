@extends('exports.pdf-base')

@section('titulo', 'Listado de Categorías')
@section('subtitulo', '· '.$categorias->count().' categorías')

@section('content')
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th class="text-right">Productos</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categorias as $categoria)
        <tr>
            <td>{{ $categoria->nombre }}</td>
            <td>{{ $categoria->descripcion ?? '—' }}</td>
            <td class="text-right">{{ $categoria->productos_count }}</td>
            <td>{{ $categoria->activo ? 'Activo' : 'Inactivo' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
