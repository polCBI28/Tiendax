<x-layouts.app title="Control de Inventario">

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Control de Inventario</h2>
        <p class="font-body-md text-on-surface-variant">Gestione su stock, precios y estados de productos en tiempo real.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('productos.detalle') }}" class="flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-label-lg text-on-surface hover:bg-surface-container-low transition-all">
            <span class="material-symbols-outlined text-[18px]">table_view</span>
            Detalle completo
        </a>
        <a href="{{ route('productos.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-secondary text-on-secondary rounded-lg font-label-lg hover:opacity-90 shadow-sm transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Nuevo Producto
        </a>
    </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Total Productos</p>
        <div class="flex items-end justify-between">
            <span class="font-headline-md text-headline-md">{{ $totalProductos }}</span>
            <span class="text-primary font-label-sm">+{{ $nuevosEsteMes }} este mes</span>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Bajo Stock</p>
        <div class="flex items-end justify-between">
            <span class="font-headline-md text-headline-md text-secondary">{{ $bajoStock }}</span>
            <span class="text-secondary font-label-sm">Acción requerida</span>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Valor Total</p>
        <div class="flex items-end justify-between">
            <span class="font-headline-md text-headline-md">S/ {{ number_format($valorTotal, 0) }}</span>
            <span class="text-tertiary font-label-sm">Actualizado hoy</span>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Agotados</p>
        <div class="flex items-end justify-between">
            <span class="font-headline-md text-headline-md">{{ $agotados }}</span>
            <span class="text-on-surface-variant font-label-sm">Inactivos</span>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="bg-surface-container-lowest rounded-t-xl border-x border-t border-outline-variant p-4 flex flex-wrap items-center gap-4">
    <form method="GET" action="{{ route('productos.index') }}" class="flex flex-wrap items-center gap-4 w-full">
        <div class="flex items-center gap-2">
            <label class="font-label-sm text-on-surface-variant">Categoría:</label>
            <select name="categoria_id" onchange="this.form.submit()"
                    class="bg-surface-container-low border border-outline-variant rounded-lg px-3 py-1.5 font-body-sm outline-none focus:border-primary">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="font-label-sm text-on-surface-variant">Estado:</label>
            <select name="estado" onchange="this.form.submit()"
                    class="bg-surface-container-low border border-outline-variant rounded-lg px-3 py-1.5 font-body-sm outline-none focus:border-primary">
                <option value="">Todos los estados</option>
                <option value="en_stock"   {{ request('estado') == 'en_stock'   ? 'selected' : '' }}>En Stock</option>
                <option value="bajo_stock" {{ request('estado') == 'bajo_stock' ? 'selected' : '' }}>Bajo Stock</option>
                <option value="agotado"    {{ request('estado') == 'agotado'    ? 'selected' : '' }}>Agotado</option>
            </select>
        </div>
        <div class="ml-auto font-body-sm text-on-surface-variant">
            Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }} de {{ $productos->total() }}
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="bg-surface-container-lowest border border-outline-variant rounded-b-xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container border-b border-outline-variant">
                    <th class="px-6 py-4 font-label-lg text-on-surface">Producto</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Categoría</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Stock Actual</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Precio de Venta</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface">Estado</th>
                    <th class="px-6 py-4 font-label-lg text-on-surface text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($productos as $producto)
                <tr class="table-row-hover transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-full h-full object-cover" alt="{{ $producto->nombre }}">
                                @else
                                    <span class="material-symbols-outlined text-on-surface-variant">inventory_2</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-label-lg text-on-surface">{{ $producto->nombre }}</p>
                                <p class="font-label-sm text-on-surface-variant">SKU: {{ $producto->sku }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm">
                            {{ $producto->categoria->nombre ?? '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-mono-data {{ $producto->stock == 0 ? 'text-error' : ($producto->stock <= $producto->stock_minimo ? 'text-secondary font-bold' : 'text-on-surface') }}">
                        {{ $producto->stock }} unidades
                    </td>
                    <td class="px-6 py-4 font-mono-data text-on-surface">
                        S/ {{ number_format($producto->precio_venta, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        @if($producto->estado == 'en_stock')
                            <div class="flex items-center gap-1.5 text-tertiary">
                                <span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim"></span>
                                <span class="font-label-sm">En Stock</span>
                            </div>
                        @elseif($producto->estado == 'bajo_stock')
                            <div class="flex items-center gap-1.5 text-secondary">
                                <span class="w-2 h-2 rounded-full bg-secondary"></span>
                                <span class="font-label-sm font-bold">Bajo Stock</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-error">
                                <span class="w-2 h-2 rounded-full bg-error"></span>
                                <span class="font-label-sm">Agotado</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            <a href="{{ route('productos.show', $producto) }}"
                               class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all" title="Ver Historial">
                                <span class="material-symbols-outlined text-[20px]">history</span>
                            </a>
                            <a href="{{ route('productos.edit', $producto) }}"
                               class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all" title="Editar">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-lg transition-all" title="Eliminar">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-on-surface-variant font-label-lg">
                        No hay productos aún.
                        <a href="{{ route('productos.create') }}" class="text-primary hover:underline ml-1">Agregar uno</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="p-4 bg-surface border-t border-outline-variant">
        {{ $productos->appends(request()->query())->links() }}
    </div>
</div>

{{-- Gráfico + Alertas --}}
<div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-headline-md text-headline-md text-on-surface">Tendencia de Movimientos</h3>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="font-label-sm text-on-surface-variant">Entradas</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-secondary"></span>
                    <span class="font-label-sm text-on-surface-variant">Salidas</span>
                </div>
            </div>
        </div>
        <div class="h-48 w-full flex items-end gap-2 relative">
            @php
                $barras = [
                    ['h'=>'40%','color'=>'bg-primary/20 hover:bg-primary'],
                    ['h'=>'65%','color'=>'bg-secondary/20 hover:bg-secondary'],
                    ['h'=>'55%','color'=>'bg-primary/20 hover:bg-primary'],
                    ['h'=>'30%','color'=>'bg-secondary/20 hover:bg-secondary'],
                    ['h'=>'85%','color'=>'bg-primary/20 hover:bg-primary'],
                    ['h'=>'70%','color'=>'bg-secondary/20 hover:bg-secondary'],
                    ['h'=>'45%','color'=>'bg-primary/20 hover:bg-primary'],
                    ['h'=>'90%','color'=>'bg-secondary/20 hover:bg-secondary'],
                    ['h'=>'60%','color'=>'bg-primary/20 hover:bg-primary'],
                    ['h'=>'50%','color'=>'bg-secondary/20 hover:bg-secondary'],
                ];
            @endphp
            @foreach($barras as $barra)
            <div class="flex-1 {{ $barra['color'] }} transition-all rounded-t-sm" style="height: {{ $barra['h'] }}"></div>
            @endforeach
            <div class="absolute bottom-0 left-0 w-full h-px bg-outline-variant"></div>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
        <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Alertas Recientes</h3>
        <div class="space-y-3">
            @forelse($alertas as $alerta)
            <div class="flex gap-3 p-3 bg-error-container/20 rounded-lg border border-error/10">
                <span class="material-symbols-outlined text-error">warning</span>
                <div>
                    <p class="font-label-lg text-on-surface">Stock Crítico</p>
                    <p class="font-label-sm text-on-surface-variant">{{ $alerta->nombre }} ({{ $alerta->stock }} u.)</p>
                </div>
            </div>
            @empty
            <div class="flex gap-3 p-3 bg-surface-container-low rounded-lg">
                <span class="material-symbols-outlined text-on-surface-variant">check_circle</span>
                <p class="font-label-sm text-on-surface-variant">Sin alertas activas</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

</x-layouts.app>
