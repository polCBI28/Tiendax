@extends('errors.layout')

@section('color', 'amber')
@section('icon', 'schedule')
@section('codigo', '419')
@section('titulo', 'La página ha expirado')
@section('descripcion', 'Tu sesión de formulario caducó por inactividad. Recarga la página e inténtalo de nuevo.')

@section('acciones')
    <flux:button variant="filled" onclick="window.location.reload()" class="w-full justify-center">
        Recargar página
    </flux:button>
@endsection
