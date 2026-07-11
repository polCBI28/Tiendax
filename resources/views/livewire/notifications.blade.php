<?php

use App\Models\Producto;
use Livewire\Volt\Component;

new class extends Component {
    public int $total = 0;

    public function mount(): void
    {
        $this->total = Producto::where('estado', 'bajo_stock')
            ->orWhereColumn('stock', '<=', 'stock_minimo')
            ->count();
    }
}; ?>

<div>
    <flux:dropdown position="bottom" align="end">
        <flux:navbar.item icon="bell" class="relative cursor-pointer" label="Notificaciones">
            @if($total > 0)
                <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ min($total, 99) }}</span>
            @endif
        </flux:navbar.item>

        <flux:menu class="w-[320px]">
            <div class="p-3 border-b border-zinc-200 dark:border-zinc-700">
                <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Notificaciones</p>
                <p class="text-xs text-zinc-500">{{ $total }} producto(s) con stock bajo</p>
            </div>

            <div class="max-h-64 overflow-y-auto">
                @php
                    $productos = Producto::where('estado', 'bajo_stock')
                        ->orWhereColumn('stock', '<=', 'stock_minimo')
                        ->limit(20)
                        ->get();
                @endphp

                @forelse($productos as $producto)
                    <flux:menu.item as="a" :href="route('productos.show', $producto)" class="flex items-start gap-3 px-3 py-2.5">
                        <div class="shrink-0 w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <flux:icon name="archive-box" class="w-4 h-4 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $producto->nombre }}</p>
                            <p class="text-xs text-zinc-500">
                                Stock: <span class="font-semibold text-red-600 dark:text-red-400">{{ $producto->stock }}</span>
                                @if($producto->stock_minimo)
                                    / Mín: {{ $producto->stock_minimo }}
                                @endif
                            </p>
                        </div>
                    </flux:menu.item>
                @empty
                    <div class="flex flex-col items-center gap-2 py-8 text-zinc-400">
                        <flux:icon name="check-circle" class="w-8 h-8 text-green-400" />
                        <p class="text-sm">No hay productos con stock bajo</p>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-700 p-2">
                <flux:menu.item as="a" :href="route('productos.index')" icon="arrow-right" class="text-sm">
                    Ver todos los productos
                </flux:menu.item>
            </div>
        </flux:menu>
    </flux:dropdown>
</div>
