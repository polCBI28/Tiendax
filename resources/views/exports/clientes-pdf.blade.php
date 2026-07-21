@extends('exports.pdf-base')

@section('titulo', 'Listado de Clientes')
@section('subtitulo', '· '.$clientes->count().' clientes')

@section('content')
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Teléfono</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientes as $cliente)
        <tr>
            <td>{{ $cliente->nombre }}</td>
            <td>{{ $cliente->documento ?? '—' }}</td>
            <td>{{ $cliente->telefono ?? '—' }}</td>
            <td>{{ $cliente->email ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
