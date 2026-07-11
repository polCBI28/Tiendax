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
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="w-[8%]">#</flux:table.column>
                    <flux:table.column class="w-[52%]">Producto</flux:table.column>
                    <flux:table.column align="end" class="w-[20%]">Unidades</flux:table.column>
                    <flux:table.column align="end" class="w-[20%]">Ingresos</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($topProductos as $i => $item)
                    <flux:table.row wire:key="top-{{ $item->producto_id }}">
                        <flux:table.cell>{{ $i + 1 }}</flux:table.cell>
                        <flux:table.cell>
                            <p class="font-medium text-zinc-800 dark:text-white">{{ $item->producto?->nombre ?? '—' }}</p>
                            <p class="text-xs text-zinc-400">{{ $item->producto?->sku }}</p>
                        </flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($item->total_vendido) }}</flux:table.cell>
                        <flux:table.cell align="end" variant="strong" class="text-emerald-600 dark:text-emerald-400">S/ {{ number_format($item->total_ingresos, 2) }}</flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <div class="flex flex-col items-center gap-3 py-12 text-zinc-400">
                                <flux:icon.chart-bar class="size-10" />
                                <flux:text>Sin datos de ventas aún.</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
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
