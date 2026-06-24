<x-layouts.app title="Movimientos de Stock">

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Movimientos de Stock</h2>
        <p class="font-body-md text-on-surface-variant">Resumen diario de salidas por ventas y ajustes de inventario.</p>
    </div>
    <a href="{{ route('movimientos.create') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-xl font-label-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
        <span class="material-symbols-outlined">tune</span>
        Ajuste de Stock
    </a>
</div>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-center">Tipo movimiento</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-center">Ventas</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-center">Uds. vendidas</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-right">Total del día</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider">Top categorías</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-center">Ajustes</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
                @forelse($resumenDiario as $dia)
                @php
                    $fechaStr = $dia->fecha;
                    $fechaCarbon = \Carbon\Carbon::parse($fechaStr);
                    $unidades = $unidadesPorDia[$fechaStr] ?? 0;
                    $cats = $categoriasPorDia[$fechaStr] ?? collect();
                    $tiposMov = $movimientosTipoPorDia[$fechaStr] ?? collect();
                    $entradas = $tiposMov['entrada'] ?? null;
                    $salidas = $tiposMov['salida'] ?? null;
                    $ajustes = $ajustesPorDia[$fechaStr] ?? 0;
                    $esHoy = $fechaCarbon->isToday();
                    $esAyer = $fechaCarbon->isYesterday();
                @endphp
                <tr class="hover:bg-surface-container-low/50 transition-colors cursor-pointer group"
                    onclick="window.location='{{ route('movimientos.show', $fechaStr) }}'">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                {{ $esHoy ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                <span class="font-headline-sm">{{ $fechaCarbon->format('d') }}</span>
                            </div>
                            <div>
                                <p class="font-label-lg text-on-surface">
                                    @if($esHoy) Hoy
                                    @elseif($esAyer) Ayer
                                    @else {{ $fechaCarbon->translatedFormat('l') }}
                                    @endif
                                </p>
                                <p class="font-label-sm text-on-surface-variant">{{ $fechaCarbon->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col items-center gap-1.5">
                            @if($entradas)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 rounded-full font-label-sm whitespace-nowrap">
                                <span class="material-symbols-outlined text-[14px]">arrow_downward</span>
                                {{ $entradas->total }} entrada{{ $entradas->total > 1 ? 's' : '' }}
                                <span class="text-green-500 font-normal">({{ number_format($entradas->unidades) }} uds)</span>
                            </span>
                            @endif
                            @if($salidas)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-error/10 text-error rounded-full font-label-sm whitespace-nowrap">
                                <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
                                {{ $salidas->total }} salida{{ $salidas->total > 1 ? 's' : '' }}
                                <span class="text-error/60 font-normal">({{ number_format($salidas->unidades) }} uds)</span>
                            </span>
                            @endif
                            @if(!$entradas && !$salidas)
                                <span class="text-outline font-label-sm">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary/10 text-primary rounded-full font-label-lg">
                            <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                            {{ $dia->num_ventas }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-label-lg text-on-surface">{{ number_format($unidades) }}</span>
                        <span class="font-label-sm text-outline"> uds</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-headline-sm text-on-surface">S/ {{ number_format($dia->total_dia, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($cats->count())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($cats as $cat)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-label-sm whitespace-nowrap
                                {{ $loop->first ? 'bg-tertiary/10 text-tertiary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $cat->icono ?? 'category' }}</span>
                                {{ $cat->categoria }}
                                <span class="opacity-60">S/ {{ number_format($cat->total_cat, 0) }}</span>
                            </span>
                            @endforeach
                        </div>
                        @else
                            <span class="text-outline font-label-sm">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($ajustes > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-secondary/10 text-secondary rounded-full font-label-sm">
                            <span class="material-symbols-outlined text-[14px]">tune</span>
                            {{ $ajustes }}
                        </span>
                        @else
                            <span class="text-outline font-label-sm">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">chevron_right</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center text-on-surface-variant font-label-lg">
                        <div class="flex flex-col items-center gap-3 text-outline">
                            <span class="material-symbols-outlined text-[48px]">swap_vert</span>
                            <p class="font-body-md">No hay movimientos registrados aún.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($resumenDiario->hasPages())
    <div class="p-4 bg-surface border-t border-outline-variant">
        {{ $resumenDiario->links() }}
    </div>
    @endif
</div>

</x-layouts.app>
