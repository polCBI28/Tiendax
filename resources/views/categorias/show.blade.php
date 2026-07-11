<x-layouts.app title="{{ $categoria->nombre }}">

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 mb-6 font-label-sm text-outline">
    <a href="{{ route('categorias.index') }}" class="hover:text-primary transition-colors">Catálogo</a>
    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
    <span class="text-on-surface">{{ $categoria->nombre }}</span>
</nav>

{{-- Hero banner --}}
<div class="relative rounded-2xl overflow-hidden mb-8 h-48">
    @if($categoria->imagen)
        <img src="{{ asset('storage/' . $categoria->imagen) }}" class="w-full h-full object-cover" alt="{{ $categoria->nombre }}">
    @else
        <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent flex items-end p-8">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 shrink-0">
                <span class="material-symbols-outlined text-white text-[32px]">{{ $categoria->icono ?? 'category' }}</span>
            </div>
            <div class="min-w-0">
                <h2 class="font-headline-lg text-headline-lg text-white">{{ $categoria->nombre }}</h2>
                @if($categoria->descripcion)
                    <p class="font-body-sm text-white/70 mt-0.5 truncate">{{ $categoria->descripcion }}</p>
                @endif
            </div>
        </div>
        <a href="{{ route('categorias.index', ['editar' => $categoria->id]) }}"
           class="shrink-0 px-3 py-1.5 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white font-label-sm hover:bg-white/30 transition flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">edit</span> Editar
        </a>
    </div>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">inventory_2</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $totalProductos }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">Productos</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-secondary/10 text-secondary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">account_tree</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $categoria->subcategorias->count() }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">Subcategorías</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-tertiary/10 text-tertiary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $enStock }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">En stock</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-error/10 text-error rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">warning</span>
        </div>
        <div>
            <p class="font-headline-md text-on-surface leading-none">{{ $conProblemas }}</p>
            <p class="font-label-sm text-on-surface-variant mt-1">Con problemas</p>
        </div>
    </div>
</div>

{{-- Cabecera subcategorías --}}
<div class="flex items-center justify-between mb-4">
    <h3 class="font-headline-md text-headline-md text-on-surface">Subcategorías</h3>
    <a href="{{ route('subcategorias.index', ['crear' => 1]) }}"
       class="inline-flex items-center gap-1 px-3 py-1.5 bg-secondary/10 text-secondary rounded-lg font-label-sm hover:bg-secondary/20 transition-all">
        <span class="material-symbols-outlined text-[16px]">add</span>
        Nueva subcategoría
    </a>
</div>

{{-- Grid de subcategorías --}}
@if($categoria->subcategorias->isEmpty())
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant py-16 text-center text-on-surface-variant">
    <span class="material-symbols-outlined text-[48px] block mb-3 opacity-30">account_tree</span>
    <p class="font-label-lg">No hay subcategorías en esta categoría.</p>
    <a href="{{ route('subcategorias.index', ['crear' => 1]) }}" class="text-primary font-label-sm hover:underline mt-2 inline-block">
        Agregar la primera
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($categoria->subcategorias as $sub)
    @php
        $total    = $sub->productos_count;
        $ok       = $sub->en_stock ?? 0;
        $problema = $sub->bajo_stock ?? 0;
        $pct      = $total > 0 ? round(($ok / $total) * 100) : 0;
        $barColor = $pct >= 70 ? 'bg-tertiary' : ($pct >= 40 ? 'bg-secondary' : 'bg-error');
    @endphp
    <a href="{{ route('subcategorias.show', $sub) }}"
       class="group bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm hover:shadow-md hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-200 p-5 flex flex-col gap-4">

        {{-- Header de la card --}}
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center shrink-0 group-hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">{{ $categoria->icono ?? 'label' }}</span>
                </div>
                <div class="min-w-0">
                    <p class="font-label-lg text-on-surface group-hover:text-primary transition-colors truncate">{{ $sub->nombre }}</p>
                    @if($sub->descripcion)
                        <p class="font-label-sm text-outline truncate">{{ $sub->descripcion }}</p>
                    @endif
                </div>
            </div>
            <span class="material-symbols-outlined text-outline text-[20px] group-hover:translate-x-1 group-hover:text-primary transition-all shrink-0 ml-2">
                chevron_right
            </span>
        </div>

        {{-- Contadores --}}
        <div class="flex items-center gap-4">
            <span class="font-label-sm text-on-surface-variant">
                <span class="font-bold text-on-surface">{{ $total }}</span> producto{{ $total !== 1 ? 's' : '' }}
            </span>
            @if($problema > 0)
                <span class="font-label-sm text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">warning</span>
                    {{ $problema }} con problema
                </span>
            @endif
        </div>

        {{-- Barra de salud de stock --}}
        @if($total > 0)
        <div>
            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $pct }}%"></div>
            </div>
            <p class="font-label-sm text-outline mt-1">{{ $pct }}% en stock saludable</p>
        </div>
        @else
        <p class="font-label-sm text-outline">Sin productos aún</p>
        @endif

        @if(!$sub->activo)
            <span class="self-start px-2 py-0.5 bg-surface-container-high text-outline font-label-sm rounded-full">Inactiva</span>
        @endif
    </a>
    @endforeach
</div>
@endif

</x-layouts.app>
