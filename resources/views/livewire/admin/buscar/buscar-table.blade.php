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
                <flux:card class="p-0 overflow-hidden">
                    <flux:table>
                        <flux:table.rows>
                            @foreach($productos as $p)
                            <flux:table.row wire:key="prod-{{ $p->id }}">
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-zinc-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                                            @if($p->imagen)
                                                <img src="{{ asset('storage/' . $p->imagen) }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <flux:icon.archive-box variant="mini" class="text-zinc-400" />
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('productos.show', $p) }}" class="font-medium text-zinc-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">{{ $p->nombre }}</a>
                                            <p class="text-xs text-zinc-400">SKU: {{ $p->sku }} · {{ $p->categoria?->nombre }}</p>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell align="end" variant="strong">S/ {{ number_format($p->precio_venta, 2) }}</flux:table.cell>
                                <flux:table.cell align="end">
                                    <span class="{{ $p->stock == 0 ? 'text-red-600 dark:text-red-400' : ($p->stock <= $p->stock_minimo ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400') }}">
                                        {{ $p->stock }} uds.
                                    </span>
                                </flux:table.cell>
                                <flux:table.cell align="center">
                                    <flux:button href="{{ route('productos.index', ['editar' => $p->id]) }}" icon="arrow-right" variant="ghost" size="sm" />
                                </flux:table.cell>
                            </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
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
                <flux:card class="p-0 overflow-hidden">
                    <flux:table>
                        <flux:table.rows>
                            @foreach($clientes as $c)
                            <flux:table.row wire:key="cli-{{ $c->id }}">
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-indigo-500/10 flex items-center justify-center shrink-0">
                                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm uppercase">{{ substr($c->nombre, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <a href="{{ route('clientes.show', $c) }}" class="font-medium text-zinc-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">{{ $c->nombre }}</a>
                                            <p class="text-xs text-zinc-400">{{ $c->documento ?? 'Sin documento' }}{{ $c->telefono ? ' · '.$c->telefono : '' }}</p>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>{{ $c->email ?? '—' }}</flux:table.cell>
                                <flux:table.cell align="center">
                                    <flux:button href="{{ route('clientes.show', $c) }}" icon="arrow-right" variant="ghost" size="sm" />
                                </flux:table.cell>
                            </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
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
                <flux:card class="p-0 overflow-hidden">
                    <flux:table>
                        <flux:table.rows>
                            @foreach($ventas as $v)
                            <flux:table.row wire:key="venta-{{ $v->id }}">
                                <flux:table.cell>
                                    <a href="{{ route('ventas.show', $v) }}" class="font-medium text-zinc-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">{{ $v->numero_boleta }}</a>
                                    <p class="text-xs text-zinc-400">{{ $v->cliente?->nombre ?? 'Sin cliente' }}</p>
                                </flux:table.cell>
                                <flux:table.cell>{{ \Illuminate\Support\Carbon::parse($v->fecha_venta)->format('d/m/Y') }}</flux:table.cell>
                                <flux:table.cell align="end" variant="strong">S/ {{ number_format($v->total, 2) }}</flux:table.cell>
                                <flux:table.cell align="center">
                                    <flux:button href="{{ route('ventas.show', $v) }}" icon="arrow-right" variant="ghost" size="sm" />
                                </flux:table.cell>
                            </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            </div>
            @endif

        </div>
    @endif

</div>
