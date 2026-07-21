<div>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <flux:breadcrumbs class="mb-1">
                <flux:breadcrumbs.item href="{{ route('movimientos.index') }}" wire:navigate>Movimientos</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $fechaCarbon->translatedFormat('d M Y') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <flux:heading size="xl">
                @if($fechaCarbon->isToday()) Hoy
                @elseif($fechaCarbon->isYesterday()) Ayer
                @else {{ $fechaCarbon->translatedFormat('l') }}
                @endif
                — {{ $fechaCarbon->translatedFormat('d \d\e F, Y') }}
            </flux:heading>
        </div>
        <flux:button href="{{ route('movimientos.index') }}" wire:navigate icon="arrow-left">Volver</flux:button>
    </div>

    {{-- KPIs del día --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <flux:card>
            <flux:subheading>Ventas</flux:subheading>
            <flux:heading size="lg" class="text-blue-600 dark:text-blue-400 mt-1">{{ $ventasDelDia->count() }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:subheading>Uds. vendidas</flux:subheading>
            <flux:heading size="lg" class="mt-1">{{ number_format($porCategoria->sum('unidades')) }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:subheading>Total del día</flux:subheading>
            <flux:heading size="lg" class="text-emerald-600 dark:text-emerald-400 mt-1">S/ {{ number_format($totalDia, 2) }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:subheading>Categorías activas</flux:subheading>
            <flux:heading size="lg" class="mt-1">{{ $porCategoria->count() }}</flux:heading>
        </flux:card>
    </div>

    {{-- Desglose por categoría --}}
    <flux:card class="overflow-hidden p-0 mb-6">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-white/10">
            <flux:heading size="sm" class="flex items-center gap-2">
                <flux:icon.tag variant="solid" class="size-5 text-blue-600 dark:text-blue-400" />
                Ventas por categoría
            </flux:heading>
        </div>

        <div class="divide-y divide-zinc-200 dark:divide-white/10">
            @foreach($porCategoria as $cat)
                @php
                    $porcentaje = $totalDia > 0 ? round(($cat->total / $totalDia) * 100, 1) : 0;
                    $productos = $productosPorCategoria[$cat->categoria_id] ?? collect();
                @endphp
                <details class="group">
                    <summary class="flex items-center gap-4 px-6 py-4 cursor-pointer hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors list-none">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[22px]">{{ $cat->icono ?? 'category' }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-zinc-800 dark:text-white">{{ $cat->categoria }}</p>
                            <div class="flex items-center gap-3 mt-1 text-sm text-zinc-400">
                                <span>{{ $cat->num_ventas }} {{ $cat->num_ventas == 1 ? 'venta' : 'ventas' }}</span>
                                <span>·</span>
                                <span>{{ number_format($cat->unidades) }} uds</span>
                            </div>
                        </div>
                        <div class="hidden md:flex items-center gap-3 w-48">
                            <div class="flex-1 h-2 bg-zinc-200 dark:bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <span class="text-sm text-zinc-400 w-12 text-right">{{ $porcentaje }}%</span>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-semibold text-zinc-800 dark:text-white">S/ {{ number_format($cat->total, 2) }}</p>
                        </div>
                        <flux:icon.chevron-down class="size-5 text-zinc-400 group-open:rotate-180 transition-transform" />
                    </summary>

                    @if($productos->count())
                        <div class="bg-zinc-50/50 dark:bg-white/5 border-t border-zinc-200 dark:border-white/10">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-zinc-200 dark:border-white/10">
                                        <th class="px-6 pl-20 py-2 text-xs uppercase tracking-wider text-zinc-400 text-left">Producto</th>
                                        <th class="px-4 py-2 text-xs uppercase tracking-wider text-zinc-400 text-center">Uds.</th>
                                        <th class="px-6 py-2 text-xs uppercase tracking-wider text-zinc-400 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200/60 dark:divide-white/5">
                                    @foreach($productos as $prod)
                                        <tr class="hover:bg-zinc-100/50 dark:hover:bg-white/5 transition-colors">
                                            <td class="px-6 pl-20 py-3">
                                                <div class="flex items-center gap-3">
                                                    @if($prod->imagen)
                                                        <img src="{{ asset('storage/' . $prod->imagen) }}" class="w-8 h-8 rounded-lg object-cover border border-zinc-200 dark:border-white/10 shrink-0">
                                                    @else
                                                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                                                            <flux:icon.archive-box variant="micro" class="text-blue-600 dark:text-blue-400" />
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium text-zinc-800 dark:text-white">{{ $prod->producto }}</p>
                                                        <p class="text-xs text-zinc-400">{{ $prod->sku }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">{{ number_format($prod->unidades) }}</td>
                                            <td class="px-6 py-3 text-right font-medium text-emerald-600 dark:text-emerald-400">S/ {{ number_format($prod->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </details>
            @endforeach
        </div>
    </flux:card>

    {{-- Ventas del día --}}
    <flux:card class="overflow-hidden p-0 mb-6">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-white/10">
            <flux:heading size="sm" class="flex items-center gap-2">
                <flux:icon.receipt-percent variant="solid" class="size-5 text-blue-600 dark:text-blue-400" />
                Ventas del día
            </flux:heading>
        </div>
        <div class="divide-y divide-zinc-200 dark:divide-white/10">
            @forelse($ventasDelDia as $venta)
                <a href="{{ route('ventas.show', $venta) }}" wire:navigate class="flex items-center gap-4 px-6 py-4 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center shrink-0">
                        <flux:icon.receipt-percent variant="mini" class="text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-zinc-800 dark:text-white">{{ $venta->numero_boleta }}</p>
                        <p class="text-xs text-zinc-400">{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-zinc-800 dark:text-white">S/ {{ number_format($venta->total, 2) }}</p>
                        <p class="text-xs text-zinc-400">{{ $venta->created_at->format('H:i') }}</p>
                    </div>
                    <flux:icon.chevron-right class="size-5 text-zinc-400" />
                </a>
            @empty
                <div class="px-6 py-8 text-center text-zinc-400">
                    No hay ventas registradas este día.
                </div>
            @endforelse
        </div>
    </flux:card>

    {{-- Ajustes manuales --}}
    @if($ajustes->count())
        <flux:card class="overflow-hidden p-0">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-white/10">
                <flux:heading size="sm" class="flex items-center gap-2">
                    <flux:icon.wrench variant="solid" class="size-5 text-amber-600 dark:text-amber-400" />
                    Ajustes de stock
                </flux:heading>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-white/10">
                @foreach($ajustes as $ajuste)
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $ajuste->tipo === 'entrada' ? 'bg-emerald-500/10' : 'bg-amber-500/10' }}">
                            <flux:icon :name="$ajuste->tipo === 'entrada' ? 'arrow-down' : 'arrow-up'" variant="mini" class="{{ $ajuste->tipo === 'entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}" />
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-zinc-800 dark:text-white">{{ $ajuste->producto?->nombre ?? '—' }}</p>
                            <p class="text-xs text-zinc-400">{{ $ajuste->motivo ?: 'Sin motivo' }} · {{ $ajuste->user?->name ?? '—' }}</p>
                        </div>
                        <span class="font-semibold {{ $ajuste->tipo === 'entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            {{ $ajuste->tipo === 'entrada' ? '+' : '-' }}{{ $ajuste->cantidad }}
                        </span>
                    </div>
                @endforeach
            </div>
        </flux:card>
    @endif

</div>
