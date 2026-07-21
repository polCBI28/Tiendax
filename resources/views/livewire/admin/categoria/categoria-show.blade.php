<div>

    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('categorias.index') }}" wire:navigate>Catálogo</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $categoria->nombre }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Hero --}}
    <flux:card class="relative overflow-hidden mb-8 p-0">
        <div class="relative h-40">
            @if($categoria->imagen)
                <img src="{{ asset('storage/' . $categoria->imagen) }}" class="w-full h-full object-cover" alt="{{ $categoria->nombre }}">
            @else
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-indigo-500/20"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent flex items-end p-6">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 shrink-0">
                        <span class="material-symbols-outlined text-white text-[32px]">{{ $categoria->icono ?? 'category' }}</span>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="lg" class="text-white">{{ $categoria->nombre }}</flux:heading>
                        @if($categoria->descripcion)
                            <p class="text-white/70 text-sm mt-0.5 truncate">{{ $categoria->descripcion }}</p>
                        @endif
                    </div>
                </div>
                <flux:button href="{{ route('categorias.index', ['editar' => $categoria->id]) }}" wire:navigate variant="ghost" class="!text-white !bg-white/20 hover:!bg-white/30 shrink-0" icon="pencil">
                    Editar
                </flux:button>
            </div>
        </div>
    </flux:card>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center size-10 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <flux:icon.archive-box variant="solid" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="leading-none">{{ $totalProductos }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Productos</flux:text>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center size-10 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shrink-0">
                    <flux:icon.rectangle-group variant="solid" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="leading-none">{{ $categoria->subcategorias->count() }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Subcategorías</flux:text>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center size-10 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <flux:icon.check-circle variant="solid" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="leading-none">{{ $enStock }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">En stock</flux:text>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center size-10 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 shrink-0">
                    <flux:icon.exclamation-triangle variant="solid" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="leading-none">{{ $conProblemas }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Con problemas</flux:text>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Subcategorías --}}
    <div class="flex items-center justify-between mb-4">
        <flux:heading size="lg">Subcategorías</flux:heading>
        <flux:button href="{{ route('subcategorias.index', ['crear' => 1]) }}" wire:navigate variant="ghost" icon="plus" size="sm">
            Nueva subcategoría
        </flux:button>
    </div>

    @if($categoria->subcategorias->isEmpty())
        <flux:card class="text-center py-16">
            <flux:icon.rectangle-group class="size-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
            <flux:text class="text-zinc-400">No hay subcategorías en esta categoría.</flux:text>
            <flux:button href="{{ route('subcategorias.index', ['crear' => 1]) }}" wire:navigate variant="ghost" size="sm" class="mt-2">
                Agregar la primera
            </flux:button>
        </flux:card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categoria->subcategorias as $sub)
                @php
                    $total = $sub->productos_count;
                    $ok = $sub->en_stock ?? 0;
                    $problema = $sub->bajo_stock ?? 0;
                    $pct = $total > 0 ? round(($ok / $total) * 100) : 0;
                    $barColor = $pct >= 70 ? 'bg-emerald-500' : ($pct >= 40 ? 'bg-amber-500' : 'bg-red-500');
                @endphp
                <flux:card as="a" href="{{ route('subcategorias.show', $sub) }}" wire:navigate class="group hover:-translate-y-0.5 hover:shadow-md transition-all duration-200 flex flex-col gap-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">{{ $categoria->icono ?? 'label' }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-zinc-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">{{ $sub->nombre }}</p>
                                @if($sub->descripcion)
                                    <p class="text-xs text-zinc-400 truncate">{{ $sub->descripcion }}</p>
                                @endif
                            </div>
                        </div>
                        <flux:icon.chevron-right class="size-5 text-zinc-400 group-hover:translate-x-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-all shrink-0 ml-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                            <span class="font-bold text-zinc-800 dark:text-white">{{ $total }}</span> producto{{ $total !== 1 ? 's' : '' }}
                        </span>
                        @if($problema > 0)
                            <span class="text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                <flux:icon.exclamation-triangle variant="micro" />
                                {{ $problema }} con problema
                            </span>
                        @endif
                    </div>

                    @if($total > 0)
                        <div>
                            <div class="w-full h-1.5 bg-zinc-200 dark:bg-white/10 rounded-full overflow-hidden">
                                <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-zinc-400 mt-1">{{ $pct }}% en stock saludable</p>
                        </div>
                    @else
                        <p class="text-xs text-zinc-400">Sin productos aún</p>
                    @endif

                    @if(!$sub->activo)
                        <flux:badge size="sm" color="zinc" class="self-start">Inactiva</flux:badge>
                    @endif
                </flux:card>
            @endforeach
        </div>
    @endif

</div>
