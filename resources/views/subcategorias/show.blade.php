<x-layouts.app title="{{ $subcategoria->nombre }}">

{{-- Breadcrumb + header --}}
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
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">{{ $subcategoria->nombre }}</h2>
            @if($subcategoria->descripcion)
                <p class="font-body-md text-on-surface-variant mt-1">{{ $subcategoria->descripcion }}</p>
            @endif
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('productos.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Agregar producto
            </a>
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
        <div class="w-10 h-10 bg-tertiary/10 text-tertiary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $subcategoria->productos->where('estado', 'en_stock')->count() }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">En stock</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-error/10 text-error rounded-lg flex items-center justify-center shrink-0">
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
            <p class="font-headline-md text-on-surface leading-none">
                S/ {{ number_format($subcategoria->productos->avg('precio_venta') ?? 0, 2) }}
            </p>
            <p class="font-label-sm text-on-surface-variant mt-1">Precio promedio</p>
        </div>
    </div>
</div>

{{-- Grid de productos --}}
@if($subcategoria->productos->isEmpty())
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant py-20 text-center text-on-surface-variant">
    <span class="material-symbols-outlined text-[56px] block mb-3 opacity-20">inventory_2</span>
    <p class="font-label-lg mb-2">No hay productos en esta subcategoría.</p>
    <a href="{{ route('productos.create') }}" class="text-primary font-label-sm hover:underline">
        Agregar el primero
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($subcategoria->productos as $producto)
    @php
        $estadoMap = [
            'en_stock'   => ['label' => 'En stock',   'class' => 'bg-tertiary/10 text-tertiary'],
            'bajo_stock' => ['label' => 'Bajo stock', 'class' => 'bg-secondary/10 text-secondary'],
            'agotado'    => ['label' => 'Agotado',    'class' => 'bg-error/10 text-error'],
        ];
        $e = $estadoMap[$producto->estado] ?? ['label' => $producto->estado, 'class' => 'bg-outline/10 text-outline'];
    @endphp
    <a href="{{ route('productos.show', $producto) }}"
       class="group bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm hover:shadow-md hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col">

        {{-- Imagen --}}
        <div class="relative h-44 bg-surface-container overflow-hidden">
            @if($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}"
                     alt="{{ $producto->nombre }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-surface-variant/20 text-[64px]">inventory_2</span>
                </div>
            @endif
            <span class="absolute top-3 right-3 px-2 py-0.5 rounded-full font-label-sm backdrop-blur-sm {{ $e['class'] }}">
                {{ $e['label'] }}
            </span>
        </div>

        {{-- Info --}}
        <div class="p-4 flex flex-col gap-1 flex-1">
            <p class="font-label-lg text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-tight">
                {{ $producto->nombre }}
            </p>
            <p class="font-label-sm text-outline">{{ $producto->sku }}</p>
            <div class="flex items-center justify-between mt-auto pt-3 border-t border-outline-variant/40">
                <span class="font-headline-sm text-on-surface">
                    S/ {{ number_format($producto->precio_venta, 2) }}
                </span>
                <span class="font-label-sm {{ $producto->stock == 0 ? 'text-error font-bold' : 'text-on-surface-variant' }}">
                    {{ $producto->stock }} uds.
                </span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

</x-layouts.app>
