<x-layouts.app title="{{ $subcategoria->nombre }}">

<div class="mb-8">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline flex-wrap">
        <a href="{{ route('categorias.index') }}" class="hover:text-primary transition-colors">Catálogo</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('categorias.show', $subcategoria->categoria) }}" class="hover:text-primary transition-colors">
            {{ $subcategoria->categoria->nombre }}
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $subcategoria->nombre }}</span>
    </nav>
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">{{ $subcategoria->nombre }}</h2>
            @if($subcategoria->descripcion)
                <p class="font-body-md text-on-surface-variant mt-1">{{ $subcategoria->descripcion }}</p>
            @endif
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('subcategorias.edit', $subcategoria) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-label-lg hover:bg-outline-variant/20 transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Editar
            </a>
        </div>
    </div>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">inventory_2</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $subcategoria->productos->count() }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">Productos</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-green-50 text-green-700 rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $subcategoria->productos->where('estado', 'en_stock')->count() }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">En stock</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-error-container/40 text-error rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">warning</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $subcategoria->productos->whereIn('estado', ['bajo_stock','agotado'])->count() }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">Bajo / Agotado</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-secondary/10 text-secondary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">payments</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">S/ {{ number_format($subcategoria->productos->avg('precio_venta') ?? 0, 2) }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">Precio promedio</p>
        </div>
    </div>
</div>

{{-- Tabla de productos --}}
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex items-center justify-between">
        <h3 class="font-headline-md text-headline-md text-on-surface">
            Productos en esta subcategoría
        </h3>
        <a href="{{ route('productos.create') }}"
           class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary rounded-lg font-label-sm hover:bg-primary/20 transition-all">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Agregar producto
        </a>
    </div>

    @if($subcategoria->productos->isEmpty())
    <div class="py-16 text-center text-on-surface-variant">
        <span class="material-symbols-outlined text-[48px] block mb-3 opacity-30">inventory_2</span>
        <p class="font-label-lg">No hay productos en esta subcategoría.</p>
        <a href="{{ route('productos.create') }}" class="text-primary font-label-sm hover:underline mt-2 inline-block">
            Agregar el primero
        </a>
    </div>
    @else
    <table class="w-full text-left">
        <thead>
            <tr class="bg-surface-container border-b border-outline-variant">
                <th class="px-6 py-3 font-label-lg text-on-surface-variant uppercase text-xs tracking-wider">Producto</th>
                <th class="px-6 py-3 font-label-lg text-on-surface-variant uppercase text-xs tracking-wider">Código</th>
                <th class="px-6 py-3 font-label-lg text-on-surface-variant uppercase text-xs tracking-wider text-center">Stock</th>
                <th class="px-6 py-3 font-label-lg text-on-surface-variant uppercase text-xs tracking-wider text-right">Precio</th>
                <th class="px-6 py-3 font-label-lg text-on-surface-variant uppercase text-xs tracking-wider text-center">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/40">
            @foreach($subcategoria->productos as $producto)
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="px-6 py-4">
                    <a href="{{ route('productos.show', $producto) }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 bg-surface-container-high rounded-lg overflow-hidden shrink-0 flex items-center justify-center">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-full h-full object-cover" alt="{{ $producto->nombre }}">
                            @else
                                <span class="material-symbols-outlined text-on-surface-variant text-[18px]">inventory_2</span>
                            @endif
                        </div>
                        <span class="font-label-lg text-on-surface group-hover:text-primary transition-colors">{{ $producto->nombre }}</span>
                    </a>
                </td>
                <td class="px-6 py-4 font-mono-data text-on-surface-variant text-sm">{{ $producto->sku }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="font-mono-data {{ $producto->stock == 0 ? 'text-error font-bold' : 'text-on-surface' }}">
                        {{ $producto->stock }}
                    </span>
                </td>
                <td class="px-6 py-4 font-mono-data text-on-surface text-right">
                    S/ {{ number_format($producto->precio_venta, 2) }}
                </td>
                <td class="px-6 py-4 text-center">
                    @if($producto->estado === 'en_stock')
                        <span class="px-2 py-1 bg-green-50 text-green-700 font-label-sm rounded-full">En stock</span>
                    @elseif($producto->estado === 'bajo_stock')
                        <span class="px-2 py-1 bg-primary-container/20 text-primary font-label-sm rounded-full">Bajo stock</span>
                    @else
                        <span class="px-2 py-1 bg-error-container/30 text-error font-label-sm rounded-full">Agotado</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

</x-layouts.app>
