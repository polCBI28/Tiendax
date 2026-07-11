<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif

    <flux:card class="p-0 overflow-hidden">
        <div wire:loading.class="opacity-60">
        <flux:table :paginate="$resumenDiario">
            <flux:table.columns>
                <flux:table.column class="w-[16%]">Fecha</flux:table.column>
                <flux:table.column align="center" class="w-[16%]">Movimiento</flux:table.column>
                <flux:table.column align="center" class="w-[8%]">Ventas</flux:table.column>
                <flux:table.column align="center" class="w-[10%]">Uds.</flux:table.column>
                <flux:table.column align="end" class="w-[10%]">Total</flux:table.column>
                <flux:table.column class="w-[26%]">Top categorías</flux:table.column>
                <flux:table.column align="center" class="w-[8%]">Ajustes</flux:table.column>
                <flux:table.column align="center" class="w-[6%]"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
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
                <flux:table.row wire:key="dia-{{ $fechaStr }}" onclick="window.location='{{ route('movimientos.show', $fechaStr) }}'" class="cursor-pointer">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $esHoy ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'bg-zinc-100 dark:bg-white/10 text-zinc-500' }}">
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
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="flex flex-col items-center gap-1">
                            @if($entradas)
                                <flux:badge size="sm" color="green" icon="arrow-down-circle">{{ $entradas->total }} ({{ number_format($entradas->unidades) }})</flux:badge>
                            @endif
                            @if($salidas)
                                <flux:badge size="sm" color="red" icon="arrow-up-circle">{{ $salidas->total }} ({{ number_format($salidas->unidades) }})</flux:badge>
                            @endif
                            @if(!$entradas && !$salidas)
                                <span class="text-zinc-400">—</span>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge size="sm" color="blue">{{ $dia->num_ventas }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="center">{{ number_format($unidades) }}</flux:table.cell>
                    <flux:table.cell align="end" variant="strong">S/ {{ number_format($dia->total_dia, 2) }}</flux:table.cell>
                    <flux:table.cell>
                        @if($cats->count())
                        <div class="flex flex-wrap gap-1">
                            @foreach($cats->take(3) as $cat)
                                <flux:badge size="sm" :color="$loop->first ? 'emerald' : 'zinc'">
                                    {{ $cat->categoria }} · S/ {{ number_format($cat->total_cat, 0) }}
                                </flux:badge>
                            @endforeach
                        </div>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        @if($ajustes > 0)
                            <flux:badge size="sm" color="amber" icon="wrench">{{ $ajustes }}</flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:icon.chevron-right variant="micro" class="text-zinc-400" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <flux:icon.arrow-path-rounded-square class="size-12" />
                            <flux:text>No hay movimientos registrados aún.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </flux:card>

</div>
