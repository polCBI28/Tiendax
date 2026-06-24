<x-layouts.app title="Ventas">

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Registro de Ventas</h2>
        <p class="font-body-md text-on-surface-variant">Historial completo de transacciones registradas.</p>
    </div>
    <a href="{{ route('ventas.create') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-xl font-label-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
        <span class="material-symbols-outlined">add_shopping_cart</span>
        Nueva Venta
    </a>
</div>

{{-- Tabla --}}
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container border-b border-outline-variant">
                    <th class="px-6 py-4 font-label-lg text-on-surface">N° Boleta</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Cliente</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Fecha</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Total</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Estado</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($ventas as $venta)
                <tr class="table-row-hover transition-colors">
                    <td class="px-6 py-4 font-mono-data text-on-surface">{{ $venta->numero_boleta }}</td>
                    <td class="px-6 py-4 font-body-sm text-on-surface">{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</td>
                    <td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 font-mono-data font-bold text-on-surface">S/ {{ number_format($venta->total, 2) }}</td>
                    <td class="px-6 py-4">
                        @if($venta->estado === 'completado')
                            <span class="px-2 py-1 bg-green-50 text-green-700 font-label-sm rounded-full">Completado</span>
                        @elseif($venta->estado === 'pendiente')
                            <span class="px-2 py-1 bg-primary-container/20 text-primary font-label-sm rounded-full">Pendiente</span>
                        @elseif($venta->estado === 'borrador')
                            <span class="px-2 py-1 bg-surface-container-high text-on-surface-variant font-label-sm rounded-full">Borrador</span>
                        @else
                            <span class="px-2 py-1 bg-error-container/20 text-error font-label-sm rounded-full">Cancelado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            <a href="{{ route('ventas.show', $venta) }}"
                               class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all" title="Ver detalle">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </a>
                            <form action="{{ route('ventas.destroy', $venta) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar esta venta?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-lg transition-all" title="Eliminar">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-on-surface-variant font-label-lg">
                        No hay ventas registradas aún.
                        <a href="{{ route('ventas.create') }}" class="text-primary hover:underline ml-1">Registrar una</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-outline-variant">
        {{ $ventas->links() }}
    </div>
</div>

</x-layouts.app>
