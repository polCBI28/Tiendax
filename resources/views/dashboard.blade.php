<x-layouts.app.sidebar title="Panel de Control">
    <div class="space-y-8">
        {{-- Header --}}
        <div>
            <flux:heading size="xl">Panel de Control</flux:heading>
            <flux:subheading>Bienvenido de nuevo, {{ auth()->user()->name }}. Aquí tienes el resumen de hoy.</flux:subheading>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon name="banknotes" variant="solid" class="text-blue-600 dark:text-blue-400 w-5 h-5" />
                    </div>
                    <flux:badge color="emerald" size="sm" inset="top bottom">+{{ $crecimiento ?? '0' }}%</flux:badge>
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Ventas del Día</flux:text>
                    <div class="flex items-baseline gap-1">
                        <flux:heading size="xl">S/ {{ number_format($ventasHoy ?? 0, 2) }}</flux:heading>
                    </div>
                </div>
            </flux:card>

            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <flux:icon name="archive-box" variant="solid" class="text-amber-600 dark:text-amber-400 w-5 h-5" />
                    </div>
                    <flux:badge color="red" size="sm" inset="top bottom">Acción Requerida</flux:badge>
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Productos Bajo Stock</flux:text>
                    <div class="flex items-baseline gap-1">
                        <flux:heading size="xl">{{ $bajoStock ?? 0 }}</flux:heading>
                        <flux:text class="text-zinc-500">Artículos</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="p-2.5 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <flux:icon name="clock" variant="solid" class="text-purple-600 dark:text-purple-400 w-5 h-5" />
                    </div>
                    <flux:badge color="amber" size="sm" inset="top bottom">En Proceso</flux:badge>
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Pedidos Pendientes</flux:text>
                    <div class="flex items-baseline gap-1">
                        <flux:heading size="xl">{{ $ventasPendientes ?? 0 }}</flux:heading>
                        <flux:text class="text-zinc-500">Pedidos</flux:text>
                    </div>
                </div>
            </flux:card>
        </div>

        {{-- Chart + Quick Access --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <flux:card class="lg:col-span-8 space-y-6">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <flux:heading>Ventas Semanales</flux:heading>
                        <flux:text>Rendimiento de los últimos 7 días</flux:text>
                    </div>
                    <flux:badge color="zinc" variant="outline" inset="top bottom">
                        {{ now()->subDays(6)->format('d/m') }} – {{ now()->format('d/m/Y') }}
                    </flux:badge>
                </div>
                <div class="relative h-64">
                    <canvas id="ventasChart"></canvas>
                    <div id="ventasChartEmpty" class="absolute inset-0 hidden items-center justify-center flex-col gap-2 text-zinc-400">
                        <flux:icon name="chart-bar" class="w-10 h-10 opacity-30" />
                        <flux:text class="opacity-50">Sin ventas en los últimos 7 días</flux:text>
                    </div>
                </div>
            </flux:card>

            <div class="lg:col-span-4 space-y-4">
                <flux:card class="space-y-4 h-full flex flex-col">
                    <flux:heading>Accesos Directos</flux:heading>

                    <flux:button :href="route('ventas.index', ['crear' => 1])" wire:navigate variant="primary" class="w-full" icon="shopping-cart">
                        Registrar Venta
                    </flux:button>

                    <div class="grid grid-cols-2 gap-3 flex-1">
                        <flux:button :href="route('productos.index')" wire:navigate variant="subtle" class="flex-col gap-2 h-auto py-4! text-center" icon="archive-box">
                            Productos
                        </flux:button>
                        <flux:button :href="route('ventas.index')" wire:navigate variant="subtle" class="flex-col gap-2 h-auto py-4! text-center" icon="document-text">
                            Ventas
                        </flux:button>
                        <flux:button :href="route('clientes.index')" wire:navigate variant="subtle" class="flex-col gap-2 h-auto py-4! text-center" icon="users">
                            Clientes
                        </flux:button>
                        <flux:button :href="route('categorias.index')" wire:navigate variant="subtle" class="flex-col gap-2 h-auto py-4! text-center" icon="tag">
                            Categorías
                        </flux:button>
                        <flux:button :href="route('movimientos.index')" wire:navigate variant="subtle" class="flex-col gap-2 h-auto py-4! text-center" icon="arrows-up-down">
                            Movimientos
                        </flux:button>
                        <flux:button :href="route('reportes.index')" wire:navigate variant="subtle" class="flex-col gap-2 h-auto py-4! text-center" icon="presentation-chart-line">
                            Reportes
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </div>

        {{-- Recent Movements --}}
        <flux:card class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading>Últimos Movimientos</flux:heading>
                <flux:button :href="route('ventas.index')" wire:navigate variant="ghost" size="sm">Ver todo</flux:button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-sm text-zinc-500 border-b border-zinc-200 dark:border-zinc-700 uppercase tracking-wider">
                            <th class="pb-3 font-semibold">Producto / Servicio</th>
                            <th class="pb-3 font-semibold">Fecha</th>
                            <th class="pb-3 font-semibold">Estado</th>
                            <th class="pb-3 font-semibold text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($ultimasVentas ?? [] as $venta)
                        @php
                            $primerProducto = $venta->detalles->first()?->producto?->nombre ?? 'Sin producto';
                            $totalItems     = $venta->detalles->count();
                            $nombreCliente  = $venta->cliente?->nombre;
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center shrink-0">
                                        <flux:icon name="document-text" class="text-zinc-500 w-4 h-4" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $primerProducto }}@if($totalItems > 1) <span class="text-zinc-400 font-normal">+{{ $totalItems - 1 }} más</span>@endif</p>
                                        <p class="text-xs text-zinc-500">
                                            {{ $venta->numero_boleta }}
                                            @if($nombreCliente) · {{ $nombreCliente }}@endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 text-sm text-zinc-500">
                                {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5">
                                @if($venta->estado === 'completado')
                                    <flux:badge color="emerald" size="sm" inset="top bottom">Completado</flux:badge>
                                @elseif($venta->estado === 'pendiente')
                                    <flux:badge color="amber" size="sm" inset="top bottom">Pendiente</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" inset="top bottom">{{ ucfirst($venta->estado) }}</flux:badge>
                                @endif
                            </td>
                            <td class="py-3.5 text-right font-mono text-sm text-zinc-900 dark:text-zinc-100">S/ {{ number_format($venta->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-zinc-400">
                                No hay movimientos aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const datos = @json($datosSemanales);
    const labels = @json($labelsSemanales);

    const canvas = document.getElementById('ventasChart');
    const emptyMsg = document.getElementById('ventasChartEmpty');

    const hayDatos = datos.some(v => v > 0);

    if (!hayDatos) {
        canvas.style.display = 'none';
        if (emptyMsg) emptyMsg.style.display = 'flex';
        return;
    }

    if (emptyMsg) emptyMsg.style.display = 'none';

    const ctx = canvas.getContext('2d');

    // El gradiente se crea dentro de un plugin para usar las dimensiones reales del chart
    const gradientPlugin = {
        id: 'dynamicGradient',
        beforeDatasetsDraw(chart) {
            const { ctx: c, chartArea: { top, bottom } } = chart;
            const grad = c.createLinearGradient(0, top, 0, bottom);
            grad.addColorStop(0, 'rgba(36, 56, 156, 0.22)');
            grad.addColorStop(1, 'rgba(36, 56, 156, 0.02)');
            chart.data.datasets[0].backgroundColor = grad;
        }
    };

    const maxValor = Math.max(...datos);

    new Chart(ctx, {
        type: 'bar',
        plugins: [gradientPlugin],
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas',
                data: datos,
                backgroundColor: 'transparent',
                borderColor: '#24389c',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(36, 56, 156, 0.32)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#ffffff',
                    borderColor: '#c5c5d4',
                    borderWidth: 1,
                    titleColor: '#454652',
                    bodyColor: '#191c1d',
                    padding: 12,
                    callbacks: {
                        label: (item) => ` S/ ${item.parsed.y.toFixed(2)}`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#454652', font: { size: 12 } }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: maxValor > 0 ? maxValor * 1.2 : 100,
                    grid: { color: '#e1e3e4', lineWidth: 1 },
                    border: { display: false, dash: [4, 4] },
                    ticks: {
                        color: '#454652',
                        font: { size: 12 },
                        callback: (val) => 'S/ ' + val.toFixed(0)
                    }
                }
            }
        }
    });
})();
</script>
@endpush

</x-layouts.app.sidebar>
