<x-layouts.app title="Detalle de Productos">

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 mb-1 font-label-sm text-outline">
            <a href="{{ route('productos.index') }}" class="hover:text-primary transition-colors">Inventario</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Detalle de Productos</span>
        </nav>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Detalle de Productos</h2>
        <p class="font-body-md text-on-surface-variant">Características completas, costos, precios y rendimiento de ventas.</p>
    </div>
    <div class="flex gap-3 shrink-0">
        <a href="{{ route('productos.index') }}"
           class="flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-label-lg text-on-surface hover:bg-surface-container-low transition-all">
            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
            Ver Inventario
        </a>
        <a href="{{ route('productos.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-secondary text-on-secondary rounded-lg font-label-lg hover:opacity-90 shadow-sm transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Nuevo Producto
        </a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('productos.detalle') }}"
      class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Buscar</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nombre o SKU..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>
        <div class="min-w-[160px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Categoría</label>
            <select name="categoria_id"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="">Todas</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Estado</label>
            <select name="estado"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="">Todos</option>
                <option value="en_stock"   {{ request('estado') === 'en_stock'   ? 'selected' : '' }}>En stock</option>
                <option value="bajo_stock" {{ request('estado') === 'bajo_stock' ? 'selected' : '' }}>Bajo stock</option>
                <option value="agotado"    {{ request('estado') === 'agotado'    ? 'selected' : '' }}>Agotado</option>
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Ordenar por</label>
            <select name="ordenar"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="nombre"              {{ request('ordenar') === 'nombre'              ? 'selected' : '' }}>Nombre</option>
                <option value="precio_venta"        {{ request('ordenar') === 'precio_venta'        ? 'selected' : '' }}>Precio venta</option>
                <option value="precio_costo"        {{ request('ordenar') === 'precio_costo'        ? 'selected' : '' }}>Precio costo</option>
                <option value="stock"               {{ request('ordenar') === 'stock'               ? 'selected' : '' }}>Stock</option>
                <option value="unidades_vendidas"   {{ request('ordenar') === 'unidades_vendidas'   ? 'selected' : '' }}>Unidades vendidas</option>
                <option value="ingresos_generados"  {{ request('ordenar') === 'ingresos_generados'  ? 'selected' : '' }}>Ingresos generados</option>
            </select>
        </div>
        <div class="min-w-[110px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Dirección</label>
            <select name="dir"
                    class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                <option value="asc"  {{ request('dir', 'asc') === 'asc'  ? 'selected' : '' }}>A → Z / Menor</option>
                <option value="desc" {{ request('dir') === 'desc' ? 'selected' : '' }}>Z → A / Mayor</option>
            </select>
        </div>
        <button type="submit"
                class="px-5 py-2 bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 active:scale-95 transition-all">
            Filtrar
        </button>
        @if(request()->hasAny(['q','categoria_id','estado','ordenar','dir']))
        <a href="{{ route('productos.detalle') }}"
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
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Producto</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">SKU</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Categoría</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Subcategoría</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Costo</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">P. Venta</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Margen</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Stock</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-center">Estado</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Uds. vendidas</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-right">Ingresos</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
                @forelse($productos as $producto)
                @php
                    $margen = $producto->precio_costo > 0
                        ? round((($producto->precio_venta - $producto->precio_costo) / $producto->precio_venta) * 100, 1)
                        : null;
                    $margenColor = $margen === null ? 'text-outline'
                        : ($margen >= 30 ? 'text-tertiary' : ($margen >= 15 ? 'text-primary' : 'text-error'));
                @endphp
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    {{-- Producto --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}"
                                     alt="{{ $producto->nombre }}"
                                     class="w-9 h-9 rounded-lg object-cover border border-outline-variant shrink-0">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 border border-primary/10">
                                    <span class="material-symbols-outlined text-primary text-[18px]">inventory_2</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-label-lg text-on-surface truncate max-w-[180px]">{{ $producto->nombre }}</p>
                                @if($producto->descripcion)
                                <p class="font-label-sm text-outline truncate max-w-[180px]">{{ $producto->descripcion }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    {{-- SKU --}}
                    <td class="px-4 py-3">
                        <span class="font-mono font-label-sm text-on-surface-variant bg-surface-container-low px-2 py-0.5 rounded">
                            {{ $producto->sku }}
                        </span>
                    </td>
                    {{-- Categoría --}}
                    <td class="px-4 py-3 font-body-sm text-on-surface whitespace-nowrap">
                        @if($producto->categoria)
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-primary text-[16px]">{{ $producto->categoria->icono ?? 'category' }}</span>
                                {{ $producto->categoria->nombre }}
                            </div>
                        @else
                            <span class="text-outline">—</span>
                        @endif
                    </td>
                    {{-- Subcategoría --}}
                    <td class="px-4 py-3 font-body-sm text-on-surface-variant whitespace-nowrap">
                        {{ $producto->subcategoria->nombre ?? '—' }}
                    </td>
                    {{-- Costo --}}
                    <td class="px-4 py-3 font-body-sm text-on-surface text-right whitespace-nowrap">
                        @if($producto->precio_costo)
                            S/ {{ number_format($producto->precio_costo, 2) }}
                        @else
                            <span class="text-outline">—</span>
                        @endif
                    </td>
                    {{-- Precio venta --}}
                    <td class="px-4 py-3 font-label-lg text-on-surface text-right whitespace-nowrap">
                        S/ {{ number_format($producto->precio_venta, 2) }}
                    </td>
                    {{-- Margen --}}
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        @if($margen !== null)
                            <span class="font-label-lg {{ $margenColor }}">{{ $margen }}%</span>
                        @else
                            <span class="font-label-sm text-outline">—</span>
                        @endif
                    </td>
                    {{-- Stock --}}
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <span class="font-label-lg text-on-surface">{{ $producto->stock }}</span>
                        <span class="font-label-sm text-outline"> / mín {{ $producto->stock_minimo }}</span>
                    </td>
                    {{-- Estado --}}
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        @php
                            $estadoMap = [
                                'en_stock'   => ['label' => 'En stock',   'class' => 'bg-tertiary/10 text-tertiary'],
                                'bajo_stock' => ['label' => 'Bajo stock', 'class' => 'bg-secondary/10 text-secondary'],
                                'agotado'    => ['label' => 'Agotado',    'class' => 'bg-error/10 text-error'],
                            ];
                            $e = $estadoMap[$producto->estado] ?? ['label' => $producto->estado, 'class' => 'bg-outline/10 text-outline'];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full font-label-sm {{ $e['class'] }}">{{ $e['label'] }}</span>
                    </td>
                    {{-- Unidades vendidas --}}
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <span class="font-label-lg text-on-surface">{{ number_format($producto->unidades_vendidas ?? 0) }}</span>
                        <span class="font-label-sm text-outline"> uds</span>
                    </td>
                    {{-- Ingresos generados --}}
                    <td class="px-4 py-3 font-label-lg text-tertiary text-right whitespace-nowrap">
                        S/ {{ number_format($producto->ingresos_generados ?? 0, 2) }}
                    </td>
                    {{-- Acciones --}}
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('productos.show', $producto) }}"
                               title="Ver detalle"
                               class="p-1.5 rounded-lg text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-all">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                            <a href="{{ route('productos.edit', $producto) }}"
                               title="Editar"
                               class="p-1.5 rounded-lg text-on-surface-variant hover:bg-secondary/10 hover:text-secondary transition-all">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-outline">
                            <span class="material-symbols-outlined text-[48px]">inventory_2</span>
                            <p class="font-body-md">No se encontraron productos con los filtros aplicados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pie de tabla: totales + paginación --}}
    @if($productos->count())
    <div class="px-4 py-3 border-t border-outline-variant bg-surface-container-low flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-wrap gap-6 font-label-sm text-on-surface-variant">
            <span>
                <span class="text-on-surface font-bold">{{ $productos->total() }}</span> productos
            </span>
            <span>
                Uds. vendidas: <span class="text-on-surface font-bold">{{ number_format($productos->sum('unidades_vendidas')) }}</span>
            </span>
            <span>
                Ingresos totales: <span class="text-tertiary font-bold">S/ {{ number_format($productos->sum('ingresos_generados'), 2) }}</span>
            </span>
        </div>
        <div>
            {{ $productos->links() }}
        </div>
    </div>
    @endif
</div>

</x-layouts.app>
