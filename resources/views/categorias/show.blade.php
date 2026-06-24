<x-layouts.app title="Detalle de Categoría">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('categorias.index') }}" class="hover:text-primary transition-colors">Catálogo</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $categoria->nombre }}</span>
    </nav>
    <div class="flex items-center justify-between">
        <h2 class="font-headline-lg text-headline-lg text-on-surface">{{ $categoria->nombre }}</h2>
        <a href="{{ route('categorias.edit', $categoria) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-label-lg hover:bg-outline-variant/20 transition-all">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Editar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="space-y-4">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            @if($categoria->imagen)
                <img src="{{ asset('storage/' . $categoria->imagen) }}" class="w-full h-40 object-cover" alt="{{ $categoria->nombre }}">
            @else
                <div class="w-full h-40 bg-primary-container/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-[48px]">category</span>
                </div>
            @endif
            <div class="p-6 space-y-3">
                <div>
                    <p class="font-label-sm text-on-surface-variant mb-1">Descripción</p>
                    <p class="font-body-sm text-on-surface">{{ $categoria->descripcion ?: 'Sin descripción.' }}</p>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-outline-variant">
                    <span class="font-label-sm text-on-surface-variant">Estado</span>
                    @if($categoria->activo)
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 font-label-sm rounded-full">Activo</span>
                    @else
                        <span class="px-2 py-0.5 bg-surface-container-high text-on-surface-variant font-label-sm rounded-full">Inactivo</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 text-center">
                <p class="font-headline-md text-primary">{{ $categoria->productos->count() }}</p>
                <p class="font-label-sm text-on-surface-variant mt-1">Productos</p>
            </div>
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 text-center">
                <p class="font-headline-md text-secondary">{{ $categoria->subcategorias->count() }}</p>
                <p class="font-label-sm text-on-surface-variant mt-1">Subcategorías</p>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">

        {{-- Subcategorías --}}
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-6 border-b border-outline-variant flex items-center justify-between">
                <h3 class="font-headline-md text-headline-md text-on-surface">Subcategorías</h3>
                <a href="{{ route('subcategorias.create') }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-secondary/10 text-secondary rounded-lg font-label-sm hover:bg-secondary/20 transition-all">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Agregar
                </a>
            </div>
            @forelse($categoria->subcategorias as $sub)
            <a href="{{ route('subcategorias.show', $sub) }}"
               class="flex items-center justify-between px-6 py-4 border-b border-outline-variant last:border-0 hover:bg-surface-container-low transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-secondary/10 text-secondary rounded-lg flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]">label</span>
                    </div>
                    <div>
                        <p class="font-label-lg text-on-surface">{{ $sub->nombre }}</p>
                        <p class="font-label-sm text-on-surface-variant">{{ $sub->productos->count() }} producto{{ $sub->productos->count() !== 1 ? 's' : '' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($sub->activo)
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 font-label-sm rounded-full">Activo</span>
                    @else
                        <span class="px-2 py-0.5 bg-surface-container-high text-on-surface-variant font-label-sm rounded-full">Inactivo</span>
                    @endif
                    <span class="material-symbols-outlined text-outline text-[20px] group-hover:translate-x-1 transition-transform">chevron_right</span>
                </div>
            </a>
            @empty
            <div class="py-8 text-center text-on-surface-variant font-label-lg">Sin subcategorías.</div>
            @endforelse
        </div>

        {{-- Productos recientes --}}
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-6 border-b border-outline-variant">
                <h3 class="font-headline-md text-headline-md text-on-surface">Productos en esta Categoría</h3>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-container border-b border-outline-variant">
                        <th class="px-6 py-3 font-label-lg text-on-surface text-left">Producto</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-right">Stock</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-right">Precio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($categoria->productos->take(8) as $prod)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-3">
                            <a href="{{ route('productos.show', $prod) }}" class="font-body-sm text-on-surface hover:text-primary transition-colors">
                                {{ $prod->nombre }}
                            </a>
                        </td>
                        <td class="px-6 py-3 font-mono-data text-on-surface text-right {{ $prod->stock == 0 ? 'text-error' : '' }}">
                            {{ $prod->stock }}
                        </td>
                        <td class="px-6 py-3 font-mono-data text-on-surface text-right">
                            S/ {{ number_format($prod->precio_venta, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-10 text-center text-on-surface-variant font-label-lg">Sin productos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($categoria->productos->count() > 8)
            <div class="p-4 border-t border-outline-variant text-center">
                <a href="{{ route('productos.index', ['categoria_id' => $categoria->id]) }}"
                   class="font-label-sm text-primary hover:underline">
                    Ver todos los {{ $categoria->productos->count() }} productos
                </a>
            </div>
            @endif
        </div>

    </div>
</div>

</x-layouts.app>
