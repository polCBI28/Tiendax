<x-layouts.app title="Detalle del día — {{ $fechaCarbon->translatedFormat('d M Y') }}">

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 mb-1 font-label-sm text-outline">
            <a href="{{ route('movimientos.index') }}" class="hover:text-primary transition-colors">Movimientos</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">{{ $fechaCarbon->translatedFormat('d M Y') }}</span>
        </nav>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">
            @if($fechaCarbon->isToday()) Hoy
            @elseif($fechaCarbon->isYesterday()) Ayer
            @else {{ $fechaCarbon->translatedFormat('l') }}
            @endif
            — {{ $fechaCarbon->translatedFormat('d \d\e F, Y') }}
        </h2>
    </div>
    <a href="{{ route('movimientos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-label-lg text-on-surface hover:bg-surface-container-low transition-all">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Volver
    </a>
</div>

{{-- KPIs del día --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4">
        <p class="font-label-sm text-on-surface-variant mb-1">Ventas</p>
        <p class="font-headline-md text-primary">{{ $ventasDelDia->count() }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4">
        <p class="font-label-sm text-on-surface-variant mb-1">Uds. vendidas</p>
        <p class="font-headline-md text-on-surface">{{ number_format($porCategoria->sum('unidades')) }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4">
        <p class="font-label-sm text-on-surface-variant mb-1">Total del día</p>
        <p class="font-headline-md text-tertiary">S/ {{ number_format($totalDia, 2) }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4">
        <p class="font-label-sm text-on-surface-variant mb-1">Categorías activas</p>
        <p class="font-headline-md text-on-surface">{{ $porCategoria->count() }}</p>
    </div>
</div>

{{-- Desglose por categoría --}}
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-low">
        <h3 class="font-label-lg text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">category</span>
            Ventas por categoría
        </h3>
    </div>

    <div class="divide-y divide-outline-variant/40">
        @foreach($porCategoria as $cat)
        @php
            $porcentaje = $totalDia > 0 ? round(($cat->total / $totalDia) * 100, 1) : 0;
            $productos = $productosPorCategoria[$cat->categoria_id] ?? collect();
        @endphp
        <details class="group">
            <summary class="flex items-center gap-4 px-6 py-4 cursor-pointer hover:bg-surface-container-low/50 transition-colors list-none">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[22px]">{{ $cat->icono ?? 'category' }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-label-lg text-on-surface">{{ $cat->categoria }}</p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="font-label-sm text-on-surface-variant">{{ $cat->num_ventas }} {{ $cat->num_ventas == 1 ? 'venta' : 'ventas' }}</span>
                        <span class="text-outline">·</span>
                        <span class="font-label-sm text-on-surface-variant">{{ number_format($cat->unidades) }} uds</span>
                    </div>
                </div>
                {{-- Barra de porcentaje --}}
                <div class="hidden md:flex items-center gap-3 w-48">
                    <div class="flex-1 h-2 bg-surface-container-high rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $porcentaje }}%"></div>
                    </div>
                    <span class="font-label-sm text-on-surface-variant w-12 text-right">{{ $porcentaje }}%</span>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-headline-sm text-on-surface">S/ {{ number_format($cat->total, 2) }}</p>
                </div>
                <span class="material-symbols-outlined text-on-surface-variant group-open:rotate-180 transition-transform">expand_more</span>
            </summary>

            {{-- Productos de esta categoría --}}
            @if($productos->count())
            <div class="bg-surface-container-low/30 border-t border-outline-variant/40">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-outline-variant/30">
                            <th class="px-6 pl-20 py-2 font-label-sm text-outline uppercase tracking-wider text-left">Producto</th>
                            <th class="px-4 py-2 font-label-sm text-outline uppercase tracking-wider text-center">Uds.</th>
                            <th class="px-6 py-2 font-label-sm text-outline uppercase tracking-wider text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        @foreach($productos as $prod)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-6 pl-20 py-3">
                                <div class="flex items-center gap-3">
                                    @if($prod->imagen)
                                        <img src="{{ asset('storage/' . $prod->imagen) }}" class="w-8 h-8 rounded-lg object-cover border border-outline-variant shrink-0">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-primary text-[16px]">inventory_2</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-label-lg text-on-surface">{{ $prod->producto }}</p>
                                        <p class="font-label-sm text-outline">{{ $prod->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-label-lg text-on-surface">{{ number_format($prod->unidades) }}</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="font-label-lg text-tertiary">S/ {{ number_format($prod->total, 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </details>
        @endforeach
    </div>
</div>

{{-- Listado de ventas del día --}}
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-low">
        <h3 class="font-label-lg text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
            Ventas del día
        </h3>
    </div>
    <div class="divide-y divide-outline-variant/40">
        @forelse($ventasDelDia as $venta)
        <a href="{{ route('ventas.show', $venta) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-surface-container-low/50 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-tertiary/10 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-tertiary text-[20px]">receipt</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-label-lg text-on-surface">{{ $venta->numero_boleta }}</p>
                <p class="font-label-sm text-on-surface-variant">{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</p>
            </div>
            <div class="text-right">
                <p class="font-label-lg text-on-surface">S/ {{ number_format($venta->total, 2) }}</p>
                <p class="font-label-sm text-on-surface-variant">{{ $venta->created_at->format('H:i') }}</p>
            </div>
            <span class="material-symbols-outlined text-on-surface-variant">chevron_right</span>
        </a>
        @empty
        <div class="px-6 py-8 text-center text-on-surface-variant font-label-lg">
            No hay ventas registradas este día.
        </div>
        @endforelse
    </div>
</div>

{{-- Ajustes manuales --}}
@if($ajustes->count())
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-low">
        <h3 class="font-label-lg text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-[20px]">tune</span>
            Ajustes de stock
        </h3>
    </div>
    <div class="divide-y divide-outline-variant/40">
        @foreach($ajustes as $ajuste)
        <div class="flex items-center gap-4 px-6 py-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                {{ $ajuste->tipo === 'entrada' ? 'bg-tertiary/10' : 'bg-secondary/10' }}">
                <span class="material-symbols-outlined text-[20px] {{ $ajuste->tipo === 'entrada' ? 'text-tertiary' : 'text-secondary' }}">
                    {{ $ajuste->tipo === 'entrada' ? 'arrow_downward' : 'arrow_upward' }}
                </span>
            </div>
            <div class="flex-1">
                <p class="font-label-lg text-on-surface">{{ $ajuste->producto?->nombre ?? '—' }}</p>
                <p class="font-label-sm text-on-surface-variant">{{ $ajuste->motivo ?: 'Sin motivo' }} · {{ $ajuste->user?->name ?? '—' }}</p>
            </div>
            <span class="font-headline-sm {{ $ajuste->tipo === 'entrada' ? 'text-tertiary' : 'text-secondary' }}">
                {{ $ajuste->tipo === 'entrada' ? '+' : '-' }}{{ $ajuste->cantidad }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

</x-layouts.app>
