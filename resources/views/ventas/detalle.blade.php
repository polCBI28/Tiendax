<x-layouts.app title="Detalle de Ventas">

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 mb-1 font-label-sm text-outline">
            <a href="{{ route('ventas.index') }}" class="hover:text-primary transition-colors">Ventas</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Detalle de Ventas</span>
        </nav>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Detalle de Ventas</h2>
        <p class="font-body-md text-on-surface-variant">Historial detallado con productos, descuentos y rendimiento por venta.</p>
    </div>
    <div class="flex gap-3 shrink-0">
        <a href="{{ route('ventas.index') }}"
           class="flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-label-lg text-on-surface hover:bg-surface-container-low transition-all">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            Ver Registro
        </a>
        <a href="{{ route('ventas.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg font-label-lg hover:opacity-90 shadow-sm transition-all">
            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
            Nueva Venta
        </a>
    </div>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[18px]">receipt_long</span>
            </div>
            <span class="font-label-sm text-on-surface-variant">Total ventas</span>
        </div>
        <span class="font-headline-md text-on-surface">{{ number_format($estadisticas['total_ventas']) }}</span>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-tertiary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-tertiary text-[18px]">payments</span>
            </div>
            <span class="font-label-sm text-on-surface-variant">Ingresos</span>
        </div>
        <span class="font-headline-md text-on-surface">S/ {{ number_format($estadisticas['ingresos'], 2) }}</span>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary text-[18px]">percent</span>
            </div>
            <span class="font-label-sm text-on-surface-variant">Descuentos</span>
        </div>
        <span class="font-headline-md text-secondary">S/ {{ number_format($estadisticas['descuentos'], 2) }}</span>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[18px]">avg_pace</span>
            </div>
            <span class="font-label-sm text-on-surface-variant">Ticket promedio</span>
        </div>
        <span class="font-headline-md text-on-surface">S/ {{ number_format($estadisticas['ticket_promedio'], 2) }}</span>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('ventas.detalle') }}"
      class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Buscar</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Boleta o cliente..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>
        <div class="min-w-[140px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}"
                   class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
        </div>
        <div class="min-w-[140px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}"
                   class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
        </div>
        <div class="min-w-[140px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Cliente</label>
            <select name="cliente_id"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="">Todos</option>
                @foreach($clientes as $cli)
                    <option value="{{ $cli->id }}" {{ request('cliente_id') == $cli->id ? 'selected' : '' }}>
                        {{ $cli->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[120px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Estado</label>
            <select name="estado"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="">Todos</option>
                <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="borrador"   {{ request('estado') === 'borrador'   ? 'selected' : '' }}>Borrador</option>
                <option value="cancelado"  {{ request('estado') === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <div class="min-w-[130px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Descuento</label>
            <select name="descuento"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="">Todos</option>
                <option value="con"  {{ request('descuento') === 'con'  ? 'selected' : '' }}>Con descuento</option>
                <option value="sin"  {{ request('descuento') === 'sin'  ? 'selected' : '' }}>Sin descuento</option>
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Ordenar por</label>
            <select name="ordenar"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="fecha_venta"    {{ request('ordenar', 'fecha_venta') === 'fecha_venta'    ? 'selected' : '' }}>Fecha</option>
                <option value="total"          {{ request('ordenar') === 'total'          ? 'selected' : '' }}>Total</option>
                <option value="numero_boleta"  {{ request('ordenar') === 'numero_boleta'  ? 'selected' : '' }}>N° Boleta</option>
            </select>
        </div>
        <div class="min-w-[110px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Orden</label>
            <select name="dir"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="desc" {{ request('dir', 'desc') === 'desc' ? 'selected' : '' }}>Más reciente</option>
                <option value="asc"  {{ request('dir') === 'asc'  ? 'selected' : '' }}>Más antiguo</option>
            </select>
        </div>
        <button type="submit"
                class="px-5 py-2 bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 active:scale-95 transition-all">
            Filtrar
        </button>
        @if(request()->hasAny(['q','desde','hasta','cliente_id','estado','descuento','ordenar','dir']))
        <a href="{{ route('ventas.detalle') }}"
           class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-label-lg hover:bg-outline-variant/20 transition-all">
            Limpiar
        </a>
        @endif
    </div>
</form>

{{-- Tabla --}}
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low">
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Boleta</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Fecha</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Cliente</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Productos</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-center">Uds.</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Subtotal</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Descuento</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Total</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Adelanto</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Deuda</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-center">Estado</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Vendedor</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
                @forelse($ventas as $venta)
                @php
                    $subtotalVenta = $venta->detalles->sum('subtotal');
                    $totalUnidades = $venta->detalles->sum('cantidad');
                    $tieneDescuento = $venta->descuento_valor > 0;
                    $montoDescuento = $subtotalVenta - $venta->total;
                @endphp
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    {{-- Boleta --}}
                    <td class="px-4 py-3">
                        <a href="{{ route('ventas.show', $venta) }}" class="font-mono font-label-sm text-primary hover:underline bg-primary/5 px-2 py-0.5 rounded">
                            {{ $venta->numero_boleta }}
                        </a>
                    </td>
                    {{-- Fecha --}}
                    <td class="px-4 py-3 font-body-sm text-on-surface whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}
                    </td>
                    {{-- Cliente --}}
                    <td class="px-4 py-3 font-body-sm text-on-surface whitespace-nowrap">
                        @if($venta->cliente)
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-primary text-[16px]">person</span>
                                {{ $venta->cliente->nombre }}
                            </div>
                        @else
                            <span class="text-outline">Sin cliente</span>
                        @endif
                    </td>
                    {{-- Productos --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1 max-w-[260px]">
                            @foreach($venta->detalles->take(3) as $det)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-label-sm whitespace-nowrap
                                {{ $loop->first ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                {{ Str::limit($det->producto?->nombre ?? '—', 18) }}
                                <span class="opacity-60">x{{ $det->cantidad }}</span>
                            </span>
                            @endforeach
                            @if($venta->detalles->count() > 3)
                            <span class="px-2 py-0.5 rounded-full font-label-sm bg-outline/10 text-outline">
                                +{{ $venta->detalles->count() - 3 }} más
                            </span>
                            @endif
                        </div>
                    </td>
                    {{-- Unidades --}}
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <span class="font-label-lg text-on-surface">{{ $totalUnidades }}</span>
                        <span class="font-label-sm text-outline"> uds</span>
                    </td>
                    {{-- Subtotal --}}
                    <td class="px-4 py-3 font-body-sm text-on-surface-variant text-right whitespace-nowrap">
                        S/ {{ number_format($subtotalVenta, 2) }}
                    </td>
                    {{-- Descuento --}}
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        @if($tieneDescuento)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-label-sm bg-secondary/10 text-secondary">
                            <span class="material-symbols-outlined text-[14px]">percent</span>
                            @if($venta->descuento_tipo === 'porcentaje')
                                {{ number_format($venta->descuento_valor, 0) }}%
                            @else
                                S/ {{ number_format($venta->descuento_valor, 2) }}
                            @endif
                        </span>
                        @else
                            <span class="text-outline font-label-sm">—</span>
                        @endif
                    </td>
                    {{-- Total --}}
                    <td class="px-4 py-3 font-label-lg text-on-surface text-right whitespace-nowrap font-bold">
                        S/ {{ number_format($venta->total, 2) }}
                    </td>
                    {{-- Adelanto --}}
                    <td class="px-4 py-3 font-body-sm text-right whitespace-nowrap">
                        @if($venta->adelanto > 0)
                            <span class="text-on-surface font-mono-data">S/ {{ number_format($venta->adelanto, 2) }}</span>
                        @else
                            <span class="text-outline font-label-sm">—</span>
                        @endif
                    </td>
                    {{-- Deuda --}}
                    @php $deudaVenta = $venta->total - $venta->adelanto; @endphp
                    <td class="px-4 py-3 font-body-sm text-right whitespace-nowrap">
                        @if($deudaVenta > 0)
                            <span class="text-error font-mono-data font-bold">S/ {{ number_format($deudaVenta, 2) }}</span>
                        @else
                            <span class="text-green-600 font-label-sm flex items-center justify-end gap-0.5">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                Pagado
                            </span>
                        @endif
                    </td>
                    {{-- Estado --}}
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        @php
                            $estadoMap = [
                                'completado' => ['label' => 'Completado', 'class' => 'bg-green-50 text-green-700'],
                                'pendiente'  => ['label' => 'Pendiente',  'class' => 'bg-primary/10 text-primary'],
                                'borrador'   => ['label' => 'Borrador',   'class' => 'bg-surface-container-high text-on-surface-variant'],
                                'cancelado'  => ['label' => 'Cancelado',  'class' => 'bg-error/10 text-error'],
                            ];
                            $e = $estadoMap[$venta->estado] ?? ['label' => $venta->estado, 'class' => 'bg-outline/10 text-outline'];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full font-label-sm {{ $e['class'] }}">{{ $e['label'] }}</span>
                    </td>
                    {{-- Vendedor --}}
                    <td class="px-4 py-3 font-body-sm text-on-surface-variant whitespace-nowrap">
                        {{ $venta->user?->name ?? '—' }}
                    </td>
                    {{-- Acciones --}}
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('ventas.show', $venta) }}"
                               title="Ver detalle"
                               class="p-1.5 rounded-lg text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-all">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-outline">
                            <span class="material-symbols-outlined text-[48px]">receipt_long</span>
                            <p class="font-body-md">No se encontraron ventas con los filtros aplicados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pie de tabla: totales + paginación --}}
    @if($ventas->count())
    <div class="px-4 py-3 border-t border-outline-variant bg-surface-container-low flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-wrap gap-6 font-label-sm text-on-surface-variant">
            <span>
                <span class="text-on-surface font-bold">{{ $ventas->total() }}</span> ventas
            </span>
            <span>
                Uds. vendidas: <span class="text-on-surface font-bold">{{ number_format($ventas->sum(fn($v) => $v->detalles->sum('cantidad'))) }}</span>
            </span>
            <span>
                Total página: <span class="text-primary font-bold">S/ {{ number_format($ventas->sum('total'), 2) }}</span>
            </span>
            @php
                $descuentosPagina = $ventas->sum(fn($v) => $v->detalles->sum('subtotal') - $v->total);
            @endphp
            @if($descuentosPagina > 0)
            <span>
                Descuentos: <span class="text-secondary font-bold">S/ {{ number_format($descuentosPagina, 2) }}</span>
            </span>
            @endif
        </div>
        <div>
            {{ $ventas->links() }}
        </div>
    </div>
    @endif
</div>

</x-layouts.app>
