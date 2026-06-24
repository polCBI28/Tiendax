<x-layouts.app title="Reportes">

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Reportes y Análisis</h2>
        <p class="font-body-md text-on-surface-variant">Resumen de desempeño del mes {{ now()->translatedFormat('F Y') }}.</p>
    </div>
    <a href="{{ route('reportes.exportar') }}"
       class="inline-flex items-center gap-2 px-5 py-3 bg-surface-container-lowest border border-outline-variant text-on-surface rounded-xl font-label-lg hover:bg-surface-container-low shadow-sm transition-all">
        <span class="material-symbols-outlined text-[18px]">download</span>
        Exportar CSV (mes actual)
    </a>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-primary p-6 rounded-xl shadow-sm text-on-primary">
        <p class="font-label-sm uppercase tracking-wider opacity-80 mb-2">Ingresos del Mes</p>
        <p class="font-headline-md text-headline-md">S/ {{ number_format($ingresosMes, 2) }}</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Ventas Realizadas</p>
        <p class="font-headline-md text-headline-md text-on-surface">{{ $cantidadMes }}</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Ticket Promedio</p>
        <p class="font-headline-md text-headline-md text-secondary">S/ {{ number_format($ticketPromedio, 2) }}</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Unidades Vendidas</p>
        <p class="font-headline-md text-headline-md text-tertiary">{{ $unidadesMes }}</p>
    </div>
</div>

{{-- Gráfico de ventas por día --}}
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-headline-md text-headline-md text-on-surface">Ventas — Últimos 30 días</h3>
        <span class="font-label-sm text-on-surface-variant">S/ por día</span>
    </div>
    <div class="relative h-56">
        <canvas id="ventasDiarias"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

    {{-- Top productos --}}
    <div class="lg:col-span-3 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md text-on-surface">Top 10 Productos Más Vendidos</h3>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-surface-container border-b border-outline-variant">
                    <th class="px-6 py-3 font-label-lg text-on-surface text-left">#</th>
                    <th class="px-6 py-3 font-label-lg text-on-surface text-left">Producto</th>
                    <th class="px-6 py-3 font-label-lg text-on-surface text-right">Unidades</th>
                    <th class="px-6 py-3 font-label-lg text-on-surface text-right">Ingresos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($topProductos as $i => $item)
                <tr class="table-row-hover transition-colors">
                    <td class="px-6 py-3 font-label-lg text-on-surface-variant">{{ $i + 1 }}</td>
                    <td class="px-6 py-3">
                        <p class="font-label-lg text-on-surface">{{ $item->producto?->nombre ?? '—' }}</p>
                        <p class="font-label-sm text-on-surface-variant">{{ $item->producto?->sku }}</p>
                    </td>
                    <td class="px-6 py-3 font-mono-data text-on-surface text-right">{{ $item->total_vendido }}</td>
                    <td class="px-6 py-3 font-mono-data font-bold text-primary text-right">S/ {{ number_format($item->total_ingresos, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-on-surface-variant font-label-lg">Sin datos de ventas aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Resumen mensual --}}
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md text-on-surface">Resumen por Mes</h3>
        </div>
        <div class="divide-y divide-outline-variant">
            @php
                $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            @endphp
            @forelse($resumenMeses as $rm)
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="font-label-lg text-on-surface">{{ $meses[$rm->mes] }} {{ $rm->anio }}</p>
                    <p class="font-label-sm text-on-surface-variant">{{ $rm->cantidad }} {{ $rm->cantidad == 1 ? 'venta' : 'ventas' }}</p>
                </div>
                <p class="font-mono-data font-bold text-primary">S/ {{ number_format($rm->total, 2) }}</p>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-on-surface-variant font-label-lg">Sin datos históricos.</div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($diasLabels);
    const data   = @json($diasTotales);
    const maxVal = Math.max(...data, 1);

    new Chart(document.getElementById('ventasDiarias'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Ventas (S/)',
                data,
                backgroundColor: data.map(v => v > 0 ? 'rgba(36, 56, 156, 0.7)' : 'rgba(197, 197, 212, 0.4)'),
                borderRadius: 4,
                borderSkipped: false,
            }]
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
                    ticks: {
                        font: { size: 11 },
                        callback: v => 'S/ ' + v.toLocaleString('es-PE')
                    }
                }
            }
        }
    });
</script>

</x-layouts.app>
