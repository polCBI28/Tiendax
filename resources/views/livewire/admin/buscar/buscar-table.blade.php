<div>

    <div class="mb-8">
        <flux:heading size="xl">
            @if($q)
                Resultados para "{{ $q }}"
            @else
                Búsqueda
            @endif
        </flux:heading>
        @php $total = $productos->count() + $clientes->count() + $ventas->count(); @endphp
        @if($q)
            <flux:subheading>{{ $total }} {{ $total == 1 ? 'resultado' : 'resultados' }} encontrados</flux:subheading>
        @endif
    </div>

    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar productos, clientes, boletas..." class="mb-8 max-w-xl" />

    @if(strlen($q) > 0 && strlen($q) < 2)
        <flux:callout icon="information-circle" heading="Escribe al menos 2 caracteres para buscar." />
    @elseif($q && $total === 0)
        <flux:card class="text-center py-12">
            <flux:icon.magnifying-glass-circle class="size-12 mx-auto text-zinc-400 mb-3" />
            <flux:heading size="sm">Sin resultados</flux:heading>
            <flux:text class="text-zinc-400">No se encontró nada que coincida con "{{ $q }}".</flux:text>
        </flux:card>
    @elseif(!$q)
        <flux:card class="text-center py-12">
            <flux:icon.magnifying-glass class="size-12 mx-auto text-zinc-400 mb-3" />
            <flux:text class="text-zinc-400">Escribe arriba para buscar productos, clientes o ventas.</flux:text>
        </flux:card>
    @else
        <div class="space-y-8">

            {{-- Productos --}}
            @if($productos->count() > 0)
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.archive-box variant="solid" class="size-5 text-blue-600 dark:text-blue-400" />
                    <flux:heading size="sm">Productos</flux:heading>
                    <flux:badge size="sm" color="blue">{{ $productos->count() }}</flux:badge>
                </div>
                <flux:card class="overflow-hidden p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Producto</th>
                                    <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Precio</th>
                                    <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Stock</th>
                                    <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-16"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $p)
                                <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-zinc-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                                                @if($p->imagen)
                                                    <img src="{{ asset('storage/' . $p->imagen) }}" class="w-full h-full object-cover rounded-lg">
                                                @else
                                                    <flux:icon.archive-box variant="mini" class="text-zinc-400" />
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('productos.show', $p) }}" wire:navigate class="font-medium text-zinc-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">{{ $p->nombre }}</a>
                                                <p class="text-xs text-zinc-400">SKU: {{ $p->sku }} · {{ $p->categoria?->nombre }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-zinc-800 dark:text-white">S/ {{ number_format($p->precio_venta, 2) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="{{ $p->stock == 0 ? 'text-red-600 dark:text-red-400' : ($p->stock <= $p->stock_minimo ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400') }}">
                                            {{ $p->stock }} uds.
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            <a href="{{ route('productos.index', ['editar' => $p->id]) }}"
                                               wire:navigate
                                               class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                               title="Ir al producto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </flux:card>
            </div>
            @endif

            {{-- Clientes --}}
            @if($clientes->count() > 0)
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.user-group variant="solid" class="size-5 text-indigo-600 dark:text-indigo-400" />
                    <flux:heading size="sm">Clientes</flux:heading>
                    <flux:badge size="sm" color="indigo">{{ $clientes->count() }}</flux:badge>
                </div>
                <flux:card class="overflow-hidden p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Cliente</th>
                                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Email</th>
                                    <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-16"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientes as $c)
                                <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-indigo-500/10 flex items-center justify-center shrink-0">
                                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm uppercase">{{ substr($c->nombre, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <a href="{{ route('clientes.show', $c) }}" wire:navigate class="font-medium text-zinc-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">{{ $c->nombre }}</a>
                                                <p class="text-xs text-zinc-400">{{ $c->documento ?? 'Sin documento' }}{{ $c->telefono ? ' · '.$c->telefono : '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $c->email ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            <a href="{{ route('clientes.show', $c) }}"
                                               wire:navigate
                                               class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                               title="Ver cliente">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </flux:card>
            </div>
            @endif

            {{-- Ventas --}}
            @if($ventas->count() > 0)
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.receipt-percent variant="solid" class="size-5 text-emerald-600 dark:text-emerald-400" />
                    <flux:heading size="sm">Ventas</flux:heading>
                    <flux:badge size="sm" color="emerald">{{ $ventas->count() }}</flux:badge>
                </div>
                <flux:card class="overflow-hidden p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Boleta</th>
                                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Fecha</th>
                                    <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Total</th>
                                    <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-16"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventas as $v)
                                <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('ventas.show', $v) }}" wire:navigate class="font-medium text-zinc-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">{{ $v->numero_boleta }}</a>
                                        <p class="text-xs text-zinc-400">{{ $v->cliente?->nombre ?? 'Sin cliente' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ \Illuminate\Support\Carbon::parse($v->fecha_venta)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-zinc-800 dark:text-white">S/ {{ number_format($v->total, 2) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            <a href="{{ route('ventas.show', $v) }}"
                                               wire:navigate
                                               class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                               title="Ver venta">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </flux:card>
            </div>
            @endif

        </div>
    @endif

</div>
