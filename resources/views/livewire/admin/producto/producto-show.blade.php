<div>

    <div class="mb-6">
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item href="{{ route('productos.index') }}" wire:navigate>Inventario</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $producto->nombre }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ $producto->nombre }}</flux:heading>
            <flux:button href="{{ route('productos.index', ['editar' => $producto->id]) }}" wire:navigate variant="primary" icon="pencil">
                Editar
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info del producto --}}
        <flux:card>
            @if($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                     class="w-full h-48 object-cover rounded-lg mb-4 border border-zinc-200 dark:border-white/10">
            @else
                <div class="w-full h-48 bg-zinc-100 dark:bg-white/5 rounded-lg mb-4 flex items-center justify-center">
                    <flux:icon.archive-box class="size-12 text-zinc-400" />
                </div>
            @endif

            <div class="space-y-3">
                <div class="flex justify-between">
                    <flux:text size="sm" class="text-zinc-400">SKU</flux:text>
                    <span class="font-mono text-sm text-zinc-800 dark:text-white">{{ $producto->sku }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <flux:text size="sm" class="text-zinc-400">Categoría</flux:text>
                    <flux:badge size="sm" color="blue">{{ $producto->categoria?->nombre ?? '—' }}</flux:badge>
                </div>
                <div class="flex justify-between">
                    <flux:text size="sm" class="text-zinc-400">Precio Venta</flux:text>
                    <span class="font-mono font-bold text-zinc-800 dark:text-white">S/ {{ number_format($producto->precio_venta, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <flux:text size="sm" class="text-zinc-400">Precio Costo</flux:text>
                    <span class="font-mono text-zinc-800 dark:text-white">S/ {{ number_format($producto->precio_costo ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-zinc-200 dark:border-white/10">
                    <flux:text size="sm" class="text-zinc-400">Stock Actual</flux:text>
                    <flux:heading size="lg" class="{{ $producto->stock == 0 ? 'text-red-600 dark:text-red-400' : ($producto->stock <= $producto->stock_minimo ? 'text-amber-600 dark:text-amber-400' : '') }}">
                        {{ $producto->stock }}
                    </flux:heading>
                </div>
                <div class="flex justify-between">
                    <flux:text size="sm" class="text-zinc-400">Stock Mínimo</flux:text>
                    <span class="font-mono text-zinc-800 dark:text-white">{{ $producto->stock_minimo }}</span>
                </div>
            </div>
        </flux:card>

        {{-- Historial de movimientos --}}
        <flux:card class="lg:col-span-2">
            <flux:heading size="lg" class="mb-6">Historial de Movimientos</flux:heading>
            <div class="space-y-3">
                @forelse($producto->movimientos->sortByDesc('created_at') as $mov)
                    <div class="flex items-center justify-between p-3 rounded-lg border border-zinc-200 dark:border-white/10">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg {{ $mov->tipo === 'entrada' ? 'bg-green-500/10 text-green-600 dark:text-green-400' : 'bg-red-500/10 text-red-600 dark:text-red-400' }}">
                                <flux:icon :name="$mov->tipo === 'entrada' ? 'arrow-down' : 'arrow-up'" variant="micro" />
                            </div>
                            <div>
                                <p class="font-medium text-zinc-800 dark:text-white">{{ ucfirst($mov->tipo) }}</p>
                                <p class="text-xs text-zinc-400">{{ $mov->motivo }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-mono font-bold {{ $mov->tipo === 'entrada' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $mov->tipo === 'entrada' ? '+' : '-' }}{{ $mov->cantidad }}
                            </p>
                            <p class="text-xs text-zinc-400">{{ $mov->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-zinc-400">
                        Sin movimientos registrados.
                    </div>
                @endforelse
            </div>
        </flux:card>
    </div>

</div>
