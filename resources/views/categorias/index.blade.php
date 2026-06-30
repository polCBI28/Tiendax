<x-layouts.app title="Catálogo de Categorías">

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
    <div>
        <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">ShopMaster</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Categorías</span>
        </nav>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Catálogo de Categorías</h2>
        <p class="font-body-md text-on-surface-variant">Organiza y gestiona tu inventario a través de las divisiones principales del negocio.</p>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <a href="{{ route('categorias.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-xl font-label-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
            <span class="material-symbols-outlined">add</span>
            Agregar Categoría
        </a>
        <a href="{{ route('subcategorias.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-secondary text-on-secondary rounded-xl font-label-lg shadow-lg shadow-secondary/20 hover:scale-[1.02] active:scale-95 transition-all">
            <span class="material-symbols-outlined">settings_suggest</span>
            Gestionar Sub-tipos
        </a>
    </div>
</div>

{{-- Grid Bento de Categorías --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6">
    @php
        $spans    = [7, 5, 5, 7];
        $heights  = ['h-72', 'h-72', 'h-60', 'h-60'];
        $overlays = [
            'bg-gradient-to-t from-black/80 via-black/20 to-transparent',
            'bg-gradient-to-t from-primary/90 via-primary/30 to-transparent',
            'bg-gradient-to-t from-tertiary/90 via-tertiary/30 to-transparent',
            'bg-gradient-to-t from-on-secondary-fixed-variant/90 via-on-secondary-fixed-variant/20 to-transparent',
        ];
        $icons    = ['local_cafe','smart_toy','account_balance_wallet','redeem'];
        $iconBgs  = ['bg-secondary/80','bg-primary/80','bg-tertiary/80','bg-secondary-container/90'];
    @endphp

    @forelse($categorias as $index => $categoria)
    @php
        $i       = $index % 4;
        $span    = $spans[$i];
        $height  = $heights[$i];
        $overlay = $overlays[$i];
        $icon    = $categoria->icono ?? $icons[$i];
        $iconBg  = $iconBgs[$i];
    @endphp
    <div class="lg:col-span-{{ $span }} group category-card relative overflow-hidden rounded-3xl bg-surface-container-lowest border border-outline-variant shadow-sm transition-all duration-300 hover:shadow-2xl hover:scale-[1.015] cursor-pointer"
         style="transform: scale(1);">
        {{-- Link que cubre toda la tarjeta --}}
        <a href="{{ route('categorias.show', $categoria) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $categoria->nombre }}"></a>
        <div class="absolute inset-0 z-0">
            @if($categoria->imagen)
                <img class="category-image w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                     src="{{ asset('storage/' . $categoria->imagen) }}"
                     alt="{{ $categoria->nombre }}">
            @else
                <div class="w-full h-full bg-gradient-to-br from-primary-fixed to-secondary-fixed"></div>
            @endif
            <div class="absolute inset-0 {{ $overlay }}"></div>
        </div>
        <div class="relative {{ $height }} flex flex-col justify-end p-8 text-white">
            {{-- Indicador "Ver subcategorías" en hover --}}
            <div class="absolute top-5 right-5 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0 bg-white/20 backdrop-blur-md rounded-full px-3 py-1 border border-white/30">
                <span class="font-label-sm text-white">Ver subcategorías</span>
                <span class="material-symbols-outlined text-white text-[16px]">arrow_forward</span>
            </div>

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="p-3 glass-effect rounded-2xl">
                        <span class="material-symbols-outlined text-3xl">{{ $icon }}</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md">{{ $categoria->nombre }}</h3>
                </div>
                <span class="bg-white/20 backdrop-blur-md px-4 py-1 rounded-full font-label-lg border border-white/30">
                    {{ $categoria->productos_count }} artículos
                </span>
            </div>
            @if($categoria->descripcion)
                <p class="text-white/80 font-body-sm max-w-md">{{ $categoria->descripcion }}</p>
            @endif
            <div class="relative z-20 mt-4 flex gap-2">
                <a href="{{ route('categorias.edit', $categoria) }}"
                   class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-white font-label-sm hover:bg-white/30 transition border border-white/20 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">edit</span> Editar
                </a>
                <form action="{{ route('categorias.destroy', $categoria) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar esta categoría?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1 bg-error/50 backdrop-blur-md rounded-lg text-white font-label-sm hover:bg-error/70 transition border border-white/20 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">delete</span> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="lg:col-span-12 py-16 text-center text-on-surface-variant font-label-lg bg-surface-container-lowest rounded-3xl border border-outline-variant">
        No hay categorías aún.
        <a href="{{ route('categorias.create') }}" class="text-primary hover:underline ml-1">Agregar una</a>
    </div>
    @endforelse
</div>

{{-- Estadísticas --}}
<section class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl flex items-center gap-4">
        <div class="h-12 w-12 bg-primary/10 text-primary rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined">inventory</span>
        </div>
        <div>
            <p class="text-outline font-label-sm uppercase tracking-wider">Total Productos</p>
            <p class="font-headline-md text-headline-md leading-none">{{ $categorias->sum('productos_count') }}</p>
        </div>
    </div>
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl flex items-center gap-4">
        <div class="h-12 w-12 bg-secondary/10 text-secondary rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined">auto_awesome</span>
        </div>
        <div>
            <p class="text-outline font-label-sm uppercase tracking-wider">Nuevos Ingresos</p>
            <p class="font-headline-md text-headline-md leading-none">—</p>
        </div>
    </div>
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl flex items-center gap-4">
        <div class="h-12 w-12 bg-tertiary/10 text-tertiary rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined">low_priority</span>
        </div>
        <div>
            <p class="text-outline font-label-sm uppercase tracking-wider">Stock Crítico</p>
            <p class="font-headline-md text-headline-md leading-none text-error">{{ $stockCritico ?? 0 }}</p>
        </div>
    </div>
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl flex items-center gap-4">
        <div class="h-12 w-12 bg-surface-container-highest text-on-surface-variant rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined">layers</span>
        </div>
        <div>
            <p class="text-outline font-label-sm uppercase tracking-wider">Categorías Activas</p>
            <p class="font-headline-md text-headline-md leading-none">{{ str_pad($categorias->where('activo', true)->count(), 2, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>
</section>

</x-layouts.app>
