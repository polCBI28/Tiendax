<x-layouts.app title="Panel de Control">

{{-- Welcome Header --}}
<header class="mb-8">
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Panel de Control</h2>
    <p class="font-body-md text-on-surface-variant">Bienvenido de nuevo, {{ auth()->user()->name }}. Aquí tienes el resumen de hoy.</p>
</header>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-primary-container/10 rounded-lg text-primary">
                <span class="material-symbols-outlined">payments</span>
            </div>
            <span class="font-label-sm text-green-600 bg-green-50 px-2 py-1 rounded">+{{ $crecimiento ?? '0' }}%</span>
        </div>
        <h3 class="font-label-lg text-on-surface-variant mb-1">Ventas del Día</h3>
        <div class="flex items-baseline gap-2">
            <span class="font-headline-md text-headline-md text-on-surface">S/ {{ number_format($ventasHoy ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-secondary-container/10 rounded-lg text-secondary">
                <span class="material-symbols-outlined">inventory</span>
            </div>
            <span class="font-label-sm text-error bg-error-container/20 px-2 py-1 rounded">Acción Requerida</span>
        </div>
        <h3 class="font-label-lg text-on-surface-variant mb-1">Productos Bajo Stock</h3>
        <div class="flex items-baseline gap-2">
            <span class="font-headline-md text-headline-md text-on-surface">{{ $bajoStock ?? 0 }} Artículos</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-tertiary-container/10 rounded-lg text-tertiary">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
            <span class="font-label-sm text-tertiary bg-tertiary-fixed/20 px-2 py-1 rounded">En Proceso</span>
        </div>
        <h3 class="font-label-lg text-on-surface-variant mb-1">Pedidos Pendientes</h3>
        <div class="flex items-baseline gap-2">
            <span class="font-headline-md text-headline-md text-on-surface">{{ $ventasPendientes ?? 0 }}</span>
        </div>
    </div>
</div>

{{-- Gráfico + Accesos Directos --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <div class="lg:col-span-8 bg-surface-container-lowest p-8 rounded-xl border border-outline-variant shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">Ventas Semanales</h3>
                <p class="font-body-sm text-on-surface-variant">Rendimiento de los últimos 7 días</p>
            </div>
            <span class="font-label-sm text-on-surface-variant bg-surface-container-low px-3 py-1.5 rounded-lg border border-outline-variant">
                {{ now()->subDays(6)->format('d/m') }} – {{ now()->format('d/m/Y') }}
            </span>
        </div>
        <div class="relative h-64">
            <canvas id="ventasChart"></canvas>
        </div>
    </div>

    <div class="lg:col-span-4">
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm h-full flex flex-col">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Accesos Directos</h3>

            {{-- Acción principal --}}
            <a href="{{ route('ventas.create') }}"
               class="w-full flex items-center justify-between p-4 bg-primary text-on-primary rounded-xl hover:brightness-110 active:scale-[0.98] transition-all shadow-sm group mb-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[22px]">add_shopping_cart</span>
                    <div>
                        <span class="font-label-lg block">Registrar Venta</span>
                        <span class="font-label-sm text-on-primary/70">Punto de venta rápido</span>
                    </div>
                </div>
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">chevron_right</span>
            </a>

            {{-- Grid de accesos --}}
            <div class="grid grid-cols-2 gap-2.5 flex-1">
                <a href="{{ route('productos.index') }}"
                   class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-surface-container-high hover:bg-primary/8 border border-transparent hover:border-primary/20 active:scale-[0.97] transition-all group text-center">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/15 transition-colors">
                        <span class="material-symbols-outlined text-primary text-[22px]">inventory_2</span>
                    </div>
                    <span class="font-label-md text-on-surface leading-tight">Productos</span>
                </a>

                <a href="{{ route('ventas.index') }}"
                   class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-surface-container-high hover:bg-secondary/8 border border-transparent hover:border-secondary/20 active:scale-[0.97] transition-all group text-center">
                    <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/15 transition-colors">
                        <span class="material-symbols-outlined text-secondary text-[22px]">receipt_long</span>
                    </div>
                    <span class="font-label-md text-on-surface leading-tight">Ventas</span>
                </a>

                <a href="{{ route('clientes.index') }}"
                   class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-surface-container-high hover:bg-tertiary/8 border border-transparent hover:border-tertiary/20 active:scale-[0.97] transition-all group text-center">
                    <div class="w-10 h-10 rounded-lg bg-tertiary/10 flex items-center justify-center group-hover:bg-tertiary/15 transition-colors">
                        <span class="material-symbols-outlined text-tertiary text-[22px]">group</span>
                    </div>
                    <span class="font-label-md text-on-surface leading-tight">Clientes</span>
                </a>

                <a href="{{ route('categorias.index') }}"
                   class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-surface-container-high hover:bg-primary/8 border border-transparent hover:border-primary/20 active:scale-[0.97] transition-all group text-center">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/15 transition-colors">
                        <span class="material-symbols-outlined text-primary text-[22px]">category</span>
                    </div>
                    <span class="font-label-md text-on-surface leading-tight">Categorías</span>
                </a>

                <a href="{{ route('movimientos.index') }}"
                   class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-surface-container-high hover:bg-secondary/8 border border-transparent hover:border-secondary/20 active:scale-[0.97] transition-all group text-center">
                    <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/15 transition-colors">
                        <span class="material-symbols-outlined text-secondary text-[22px]">swap_vert</span>
                    </div>
                    <span class="font-label-md text-on-surface leading-tight">Movimientos</span>
                </a>

                <a href="{{ route('reportes.index') }}"
                   class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-surface-container-high hover:bg-tertiary/8 border border-transparent hover:border-tertiary/20 active:scale-[0.97] transition-all group text-center">
                    <div class="w-10 h-10 rounded-lg bg-tertiary/10 flex items-center justify-center group-hover:bg-tertiary/15 transition-colors">
                        <span class="material-symbols-outlined text-tertiary text-[22px]">bar_chart</span>
                    </div>
                    <span class="font-label-md text-on-surface leading-tight">Reportes</span>
                </a>
            </div>
        </div>
    </div>

</div>

{{-- Últimos Movimientos --}}
<div class="mt-8 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-headline-md text-headline-md text-on-surface">Últimos Movimientos</h3>
        <a href="{{ route('ventas.index') }}" class="text-primary font-label-lg hover:underline transition-all">Ver todo</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="font-label-sm text-outline border-b border-outline-variant/30 uppercase tracking-wider">
                    <th class="pb-4 font-semibold">Producto / Servicio</th>
                    <th class="pb-4 font-semibold">Fecha</th>
                    <th class="pb-4 font-semibold">Estado</th>
                    <th class="pb-4 font-semibold text-right">Monto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @forelse($ultimasVentas ?? [] as $venta)
                @php
                    $primerProducto = $venta->detalles->first()?->producto?->nombre ?? 'Sin producto';
                    $totalItems     = $venta->detalles->count();
                    $nombreCliente  = $venta->cliente?->nombre;
                @endphp
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-container/10 rounded-lg flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
                            </div>
                            <div>
                                <p class="font-label-lg text-on-surface">{{ $primerProducto }}@if($totalItems > 1) <span class="text-on-surface-variant font-normal">+{{ $totalItems - 1 }} más</span>@endif</p>
                                <p class="font-label-sm text-on-surface-variant">
                                    {{ $venta->numero_boleta }}
                                    @if($nombreCliente) · {{ $nombreCliente }}@endif
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 font-body-sm text-on-surface-variant">
                        {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}
                    </td>
                    <td class="py-4">
                        @if($venta->estado === 'completado')
                            <span class="px-2 py-1 bg-green-50 text-green-700 font-label-sm rounded-full font-medium">Completado</span>
                        @elseif($venta->estado === 'pendiente')
                            <span class="px-2 py-1 bg-primary-container/20 text-primary font-label-sm rounded-full font-medium">Pendiente</span>
                        @else
                            <span class="px-2 py-1 bg-surface-container-high text-on-surface-variant font-label-sm rounded-full font-medium">{{ ucfirst($venta->estado) }}</span>
                        @endif
                    </td>
                    <td class="py-4 text-right font-mono-data text-on-surface">S/ {{ number_format($venta->total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-on-surface-variant font-label-lg">
                        No hay movimientos aún.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('ventasChart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 256);
    gradient.addColorStop(0, 'rgba(36, 56, 156, 0.18)');
    gradient.addColorStop(1, 'rgba(36, 56, 156, 0.01)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labelsSemanales),
            datasets: [{
                label: 'Ventas',
                data: @json($datosSemanales),
                backgroundColor: gradient,
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

</x-layouts.app>
