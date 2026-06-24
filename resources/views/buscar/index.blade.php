<x-layouts.app title="Búsqueda">

<div class="mb-8">
    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">
        @if($q)
            Resultados para "{{ $q }}"
        @else
            Búsqueda
        @endif
    </h2>
    @php $total = $productos->count() + $clientes->count() + $ventas->count(); @endphp
    @if($q)
    <p class="font-body-md text-on-surface-variant">
        {{ $total }} {{ $total == 1 ? 'resultado' : 'resultados' }} encontrados
    </p>
    @endif
</div>

@if(strlen($q) > 0 && strlen($q) < 2)
<div class="p-6 bg-surface-container-low rounded-xl text-center text-on-surface-variant font-label-lg">
    Escribe al menos 2 caracteres para buscar.
</div>
@elseif($q && $total === 0)
<div class="p-12 bg-surface-container-lowest rounded-xl border border-outline-variant text-center">
    <span class="material-symbols-outlined text-[48px] text-on-surface-variant mb-3 block">search_off</span>
    <p class="font-headline-md text-on-surface mb-1">Sin resultados</p>
    <p class="font-body-sm text-on-surface-variant">No se encontró nada que coincida con "{{ $q }}".</p>
</div>
@elseif(!$q)
<div class="p-12 bg-surface-container-lowest rounded-xl border border-outline-variant text-center">
    <span class="material-symbols-outlined text-[48px] text-on-surface-variant mb-3 block">search</span>
    <p class="font-body-sm text-on-surface-variant">Usa la barra de búsqueda en la parte superior para buscar productos, clientes o ventas.</p>
</div>
@else

<div class="space-y-8">

    {{-- Productos --}}
    @if($productos->count() > 0)
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-primary">inventory_2</span>
            <h3 class="font-headline-md text-headline-md text-on-surface">Productos</h3>
            <span class="ml-2 px-2 py-0.5 bg-primary-container/10 text-primary font-label-sm rounded-full">{{ $productos->count() }}</span>
        </div>
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <table class="w-full">
                <tbody class="divide-y divide-outline-variant">
                    @foreach($productos as $p)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-surface-container-high flex items-center justify-center flex-shrink-0">
                                    @if($p->imagen)
                                        <img src="{{ asset('storage/' . $p->imagen) }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <span class="material-symbols-outlined text-on-surface-variant text-[18px]">inventory_2</span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('productos.show', $p) }}" class="font-label-lg text-on-surface hover:text-primary transition-colors">{{ $p->nombre }}</a>
                                    <p class="font-label-sm text-on-surface-variant">SKU: {{ $p->sku }} · {{ $p->categoria?->nombre }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono-data text-on-surface text-right">S/ {{ number_format($p->precio_venta, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="{{ $p->stock == 0 ? 'text-error' : ($p->stock <= $p->stock_minimo ? 'text-secondary' : 'text-on-surface-variant') }} font-label-sm">
                                {{ $p->stock }} uds.
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('productos.edit', $p) }}"
                               class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all inline-flex">
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Clientes --}}
    @if($clientes->count() > 0)
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-secondary">person</span>
            <h3 class="font-headline-md text-headline-md text-on-surface">Clientes</h3>
            <span class="ml-2 px-2 py-0.5 bg-secondary/10 text-secondary font-label-sm rounded-full">{{ $clientes->count() }}</span>
        </div>
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <table class="w-full">
                <tbody class="divide-y divide-outline-variant">
                    @foreach($clientes as $c)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-secondary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="font-label-lg text-secondary uppercase">{{ substr($c->nombre, 0, 1) }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('clientes.show', $c) }}" class="font-label-lg text-on-surface hover:text-primary transition-colors">{{ $c->nombre }}</a>
                                    <p class="font-label-sm text-on-surface-variant">{{ $c->documento ?? 'Sin documento' }}{{ $c->telefono ? ' · ' . $c->telefono : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $c->email ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('clientes.show', $c) }}"
                               class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all inline-flex">
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Ventas --}}
    @if($ventas->count() > 0)
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-tertiary">receipt</span>
            <h3 class="font-headline-md text-headline-md text-on-surface">Ventas</h3>
            <span class="ml-2 px-2 py-0.5 bg-tertiary/10 text-tertiary font-label-sm rounded-full">{{ $ventas->count() }}</span>
        </div>
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <table class="w-full">
                <tbody class="divide-y divide-outline-variant">
                    @foreach($ventas as $v)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('ventas.show', $v) }}" class="font-mono-data text-on-surface hover:text-primary transition-colors">{{ $v->numero_boleta }}</a>
                            <p class="font-label-sm text-on-surface-variant">{{ $v->cliente?->nombre ?? 'Sin cliente' }}</p>
                        </td>
                        <td class="px-6 py-4 font-body-sm text-on-surface-variant">
                            {{ \Carbon\Carbon::parse($v->fecha_venta)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 font-mono-data font-bold text-on-surface text-right">S/ {{ number_format($v->total, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('ventas.show', $v) }}"
                               class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all inline-flex">
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endif

</x-layouts.app>
