<x-layouts.app title="Historial de Producto">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('productos.index') }}" class="hover:text-primary transition-colors">Inventario</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $producto->nombre }}</span>
    </nav>
    <div class="flex items-center justify-between">
        <h2 class="font-headline-lg text-headline-lg text-on-surface">{{ $producto->nombre }}</h2>
        <a href="{{ route('productos.index', ['editar' => $producto->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Editar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info del producto --}}
    <div class="space-y-6">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
            @if($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                     class="w-full h-48 object-cover rounded-lg mb-4 border border-outline-variant">
            @else
                <div class="w-full h-48 bg-surface-container-high rounded-lg mb-4 flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-surface-variant" style="font-size:48px">inventory_2</span>
                </div>
            @endif

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-label-sm text-on-surface-variant">SKU</span>
                    <span class="font-mono-data text-on-surface">{{ $producto->sku }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-label-sm text-on-surface-variant">Categoría</span>
                    <span class="px-2 py-0.5 bg-primary-container/10 text-primary font-label-sm rounded-full">{{ $producto->categoria?->nombre ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-label-sm text-on-surface-variant">Precio Venta</span>
                    <span class="font-mono-data font-bold text-on-surface">S/ {{ number_format($producto->precio_venta, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-label-sm text-on-surface-variant">Precio Costo</span>
                    <span class="font-mono-data text-on-surface">S/ {{ number_format($producto->precio_costo ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-outline-variant">
                    <span class="font-label-sm text-on-surface-variant">Stock Actual</span>
                    <span class="font-headline-md text-headline-md {{ $producto->stock == 0 ? 'text-error' : ($producto->stock <= $producto->stock_minimo ? 'text-secondary' : 'text-on-surface') }}">
                        {{ $producto->stock }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="font-label-sm text-on-surface-variant">Stock Mínimo</span>
                    <span class="font-mono-data text-on-surface">{{ $producto->stock_minimo }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de movimientos --}}
    <div class="lg:col-span-2">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-6">Historial de Movimientos</h3>
            <div class="space-y-3">
                @forelse($producto->movimientos->sortByDesc('created_at') as $mov)
                <div class="flex items-center justify-between p-3 rounded-lg border border-outline-variant">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg {{ $mov->tipo === 'entrada' ? 'bg-green-50 text-green-600' : 'bg-secondary/10 text-secondary' }}">
                            <span class="material-symbols-outlined text-[18px]">{{ $mov->tipo === 'entrada' ? 'arrow_downward' : 'arrow_upward' }}</span>
                        </div>
                        <div>
                            <p class="font-label-lg text-on-surface">{{ ucfirst($mov->tipo) }}</p>
                            <p class="font-label-sm text-on-surface-variant">{{ $mov->motivo }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-mono-data font-bold {{ $mov->tipo === 'entrada' ? 'text-green-600' : 'text-secondary' }}">
                            {{ $mov->tipo === 'entrada' ? '+' : '-' }}{{ $mov->cantidad }}
                        </p>
                        <p class="font-label-sm text-on-surface-variant">{{ $mov->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-on-surface-variant font-label-lg">
                    Sin movimientos registrados.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
