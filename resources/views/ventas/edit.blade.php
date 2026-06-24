<x-layouts.app title="Editar Venta">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('ventas.index') }}" class="hover:text-primary transition-colors">Ventas</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $venta->numero_boleta }}</span>
    </nav>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Editar Venta</h2>
</div>

<div class="max-w-xl">
    <form action="{{ route('ventas.update', $venta) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 space-y-4">
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">N° Boleta</label>
                <p class="font-mono-data text-on-surface font-bold">{{ $venta->numero_boleta }}</p>
            </div>
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Cliente</label>
                <p class="font-body-sm text-on-surface">{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</p>
            </div>
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Total</label>
                <p class="font-headline-md text-primary">S/ {{ number_format($venta->total, 2) }}</p>
            </div>
            <div class="pt-4 border-t border-outline-variant">
                <label class="font-label-lg text-on-surface-variant block mb-2">Estado *</label>
                <select name="estado" required
                        class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none">
                    <option value="borrador"   {{ $venta->estado == 'borrador'   ? 'selected' : '' }}>Borrador</option>
                    <option value="pendiente"  {{ $venta->estado == 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                    <option value="completado" {{ $venta->estado == 'completado' ? 'selected' : '' }}>Completado</option>
                    <option value="cancelado"  {{ $venta->estado == 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit"
                    class="px-6 py-3 bg-primary text-on-primary rounded-xl font-label-lg shadow-sm hover:brightness-110 active:scale-95 transition-all">
                Actualizar Estado
            </button>
            <a href="{{ route('ventas.show', $venta) }}"
               class="px-6 py-3 bg-surface-container-high text-on-surface rounded-xl font-label-lg hover:bg-outline-variant/20 transition-all">
                Cancelar
            </a>
        </div>
    </form>
</div>

</x-layouts.app>
