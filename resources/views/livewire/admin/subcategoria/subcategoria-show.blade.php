<div>

    <div class="mb-8">
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item href="{{ route('categorias.index') }}" wire:navigate>Catálogo</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('categorias.show', $subcategoria->categoria) }}" wire:navigate>{{ $subcategoria->categoria->nombre }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $subcategoria->nombre }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="flex items-start justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ $subcategoria->nombre }}</flux:heading>
                @if($subcategoria->descripcion)
                    <flux:subheading>{{ $subcategoria->descripcion }}</flux:subheading>
                @endif
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <flux:button href="{{ route('productos.index', ['crear' => 1]) }}" wire:navigate variant="primary" icon="plus">
                    Agregar producto
                </flux:button>
                <flux:button href="{{ route('subcategorias.index', ['editar' => $subcategoria->id]) }}" wire:navigate icon="pencil">
                    Editar
                </flux:button>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center size-10 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <flux:icon.archive-box variant="solid" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="leading-none">{{ $subcategoria->productos->count() }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Productos</flux:text>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center size-10 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <flux:icon.check-circle variant="solid" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="leading-none">{{ $subcategoria->productos->where('estado', 'en_stock')->count() }}</flux:heading>
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
                    <flux:heading size="lg" class="leading-none">{{ $subcategoria->productos->whereIn('estado', ['bajo_stock', 'agotado'])->count() }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Bajo / Agotado</flux:text>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center size-10 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shrink-0">
                    <flux:icon.banknotes variant="solid" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="leading-none">S/ {{ number_format($subcategoria->productos->avg('precio_venta') ?? 0, 2) }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Precio promedio</flux:text>
                </div>
            </div>
        </flux:card>
    </div>

    @if($subcategoria->productos->isEmpty())
        <flux:card class="text-center py-20">
            <flux:icon.archive-box class="size-14 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
            <flux:text class="text-zinc-400 mb-2">No hay productos en esta subcategoría.</flux:text>
            <flux:button href="{{ route('productos.index', ['crear' => 1]) }}" wire:navigate variant="ghost" size="sm">
                Agregar el primero
            </flux:button>
        </flux:card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($subcategoria->productos as $producto)
                @php
                    $estadoMap = [
                        'en_stock' => ['label' => 'En stock', 'color' => 'green'],
                        'bajo_stock' => ['label' => 'Bajo stock', 'color' => 'amber'],
                        'agotado' => ['label' => 'Agotado', 'color' => 'red'],
                    ];
                    $e = $estadoMap[$producto->estado] ?? ['label' => $producto->estado, 'color' => 'zinc'];
                @endphp
                <flux:card as="a" href="{{ route('productos.show', $producto) }}" wire:navigate class="group hover:-translate-y-0.5 hover:shadow-md transition-all duration-200 overflow-hidden p-0 flex flex-col">
                    <div class="relative h-44 bg-zinc-100 dark:bg-white/5 overflow-hidden">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}"
                                 alt="{{ $producto->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <flux:icon.archive-box class="size-16 text-zinc-300 dark:text-zinc-600" />
                            </div>
                        @endif
                        <flux:badge size="sm" :color="$e['color']" class="absolute top-3 right-3">{{ $e['label'] }}</flux:badge>
                    </div>
                    <div class="p-4 flex flex-col gap-1 flex-1">
                        <p class="font-medium text-zinc-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-tight">
                            {{ $producto->nombre }}
                        </p>
                        <p class="text-xs text-zinc-400">{{ $producto->sku }}</p>
                        <div class="flex items-center justify-between mt-auto pt-3 border-t border-zinc-200 dark:border-white/10">
                            <span class="font-semibold text-zinc-800 dark:text-white">S/ {{ number_format($producto->precio_venta, 2) }}</span>
                            <span class="text-sm {{ $producto->stock == 0 ? 'text-red-600 dark:text-red-400 font-bold' : 'text-zinc-500 dark:text-zinc-400' }}">
                                {{ $producto->stock }} uds.
                            </span>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

</div>
