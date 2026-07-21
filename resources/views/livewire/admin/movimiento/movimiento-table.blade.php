<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="overflow-hidden p-0">
        {{-- Tabla --}}
        <div wire:loading.class="opacity-60" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-40">Fecha</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-44">Movimiento</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-24">Ventas</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-24">Uds.</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Total</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Top categorías</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-24">Ajustes</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-12"></th>
                    </tr>
                </thead>
                <tbody>
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
                    <tr onclick="window.location='{{ route('movimientos.show', $fechaStr) }}'"
                        class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                        {{-- Fecha --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $esHoy ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'bg-zinc-100 dark:bg-white/5 text-zinc-500' }}">
                                    <span class="text-sm font-semibold">{{ $fechaCarbon->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-800 dark:text-white">
                                        @if($esHoy) Hoy
                                        @elseif($esAyer) Ayer
                                        @else {{ $fechaCarbon->translatedFormat('l') }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-zinc-400">{{ $fechaCarbon->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Movimiento --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col items-center gap-1">
                                @if($entradas)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-green-500"></span>
                                        {{ $entradas->total }} ({{ number_format($entradas->unidades) }})
                                    </span>
                                @endif
                                @if($salidas)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-red-500"></span>
                                        {{ $salidas->total }} ({{ number_format($salidas->unidades) }})
                                    </span>
                                @endif
                                @if(!$entradas && !$salidas)
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </div>
                        </td>

                        {{-- Ventas --}}
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $dia->num_ventas }}
                            </span>
                        </td>

                        {{-- Uds --}}
                        <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">{{ number_format($unidades) }}</td>

                        {{-- Total --}}
                        <td class="px-4 py-3 text-right font-semibold text-zinc-800 dark:text-white">S/ {{ number_format($dia->total_dia, 2) }}</td>

                        {{-- Top categorías --}}
                        <td class="px-4 py-3">
                            @if($cats->count())
                            <div class="flex flex-wrap gap-1">
                                @foreach($cats->take(3) as $cat)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $loop->first ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300' }}">
                                        {{ $cat->categoria }} · S/ {{ number_format($cat->total_cat, 0) }}
                                    </span>
                                @endforeach
                            </div>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>

                        {{-- Ajustes --}}
                        <td class="px-4 py-3 text-center">
                            @if($ajustes > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-amber-500"></span>
                                    {{ $ajustes }}
                                </span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>

                        {{-- Chevron --}}
                        <td class="px-4 py-3 text-center">
                            <svg class="w-4 h-4 text-zinc-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-zinc-400">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <p>No hay movimientos registrados aún.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($resumenDiario->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-white/10">
                {{ $resumenDiario->links() }}
            </div>
        @endif
    </flux:card>

</div>
