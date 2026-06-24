<x-layouts.app title="Movimiento">
    {{-- Los movimientos no se editan; redirige al índice --}}
    <script>window.location = "{{ route('movimientos.index') }}";</script>
</x-layouts.app>
