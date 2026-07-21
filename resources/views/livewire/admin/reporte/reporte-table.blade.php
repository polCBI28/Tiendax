<div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js" data-navigate-once></script>

    {{-- Gráfico de ventas por día --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="sm">Ventas — Últimos 30 días</flux:heading>
            <flux:text size="sm" class="text-zinc-400">S/ por día</flux:text>
        </div>
        <div
            wire:ignore
            x-data="{
                labels: @js($diasLabels),
                data: @js($diasTotales),
                chart: null,
                init() {
                    if (window.__ventasDiariasChart) { window.__ventasDiariasChart.destroy(); }
                    window.__ventasDiariasChart = new Chart(this.$refs.canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: 'Ventas (S/)',
                                data: this.data,
                                backgroundColor: this.data.map(v => v > 0 ? 'rgba(59, 130, 246, 0.7)' : 'rgba(161, 161, 170, 0.3)'),
                                borderRadius: 4,
                                borderSkipped: false,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 0 } },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0,0,0,0.05)' },
                                    ticks: { font: { size: 11 }, callback: v => 'S/ ' + v.toLocaleString('es-PE') },
                                },
                            },
                        },
                    });
                }
            }"
            class="relative h-56"
        >
            <canvas x-ref="canvas"></canvas>
        </div>
    </flux:card>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Top productos --}}
        <flux:card class="lg:col-span-3 p-0 overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-white/10">
                <flux:heading size="sm">Top 10 Productos Más Vendidos</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                            <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-12">#</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Producto</th>
                            <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Unidades</th>
                            <th class="text-right px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-36">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProductos as $i => $item)
                        <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-zinc-800 dark:text-white">{{ $item->producto?->nombre ?? '—' }}</p>
                                <p class="text-xs text-zinc-400">{{ $item->producto?->sku }}</p>
                            </td>
                            <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ number_format($item->total_vendido) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($item->total_ingresos, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-zinc-400">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-10 h-10 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <p>Sin datos de ventas aún.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>

        {{-- Resumen mensual --}}
        <flux:card class="lg:col-span-2 p-0 overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-white/10">
                <flux:heading size="sm">Resumen por Mes</flux:heading>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-white/10">
                @php
                    $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                @endphp
                @forelse($resumenMeses as $rm)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-zinc-800 dark:text-white">{{ $meses[$rm->mes] }} {{ $rm->anio }}</p>
                        <p class="text-xs text-zinc-400">{{ $rm->cantidad }} {{ $rm->cantidad == 1 ? 'venta' : 'ventas' }}</p>
                    </div>
                    <p class="font-bold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($rm->total, 2) }}</p>
                </div>
                @empty
                <div class="px-4 py-12 text-center text-zinc-400">Sin datos históricos.</div>
                @endforelse
            </div>
        </flux:card>
    </div>

</div>
