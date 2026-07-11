# Recetas de componentes — tiendax (sistema legado)

> **Legado.** El módulo Producto (usado como ejemplo en varios snippets de
> abajo) ya se migró a Livewire + Flux UI y ya NO se ve así — estos snippets
> quedan como referencia del sistema que usan las páginas todavía no migradas
> (categorías, clientes, ventas, movimientos, reportes, dashboard). Para
> módulos nuevos usa [flux-patterns.md](flux-patterns.md) en su lugar.

Todos estos snippets están copiados o adaptados directamente de vistas reales
del proyecto (productos, categorías, dashboard, reportes, ventas), en su
versión previa a la migración a Flux. Útiles como punto de partida solo si
sigues trabajando dentro del sistema legado.

## Botones

```blade
{{-- Primario --}}
<button type="submit"
        class="px-5 py-2 bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 active:scale-95 transition-all">
    Filtrar
</button>

{{-- Primario, versión "grande" de header (bento/categorías) --}}
<a href="{{ route('categorias.create') }}"
   class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-xl font-label-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
    <span class="material-symbols-outlined">add</span>
    Agregar Categoría
</a>

{{-- Secundario / CTA destacado --}}
<a href="{{ route('productos.create') }}"
   class="flex items-center gap-2 px-4 py-2 bg-secondary text-on-secondary rounded-lg font-label-lg hover:opacity-90 shadow-sm transition-all">
    <span class="material-symbols-outlined text-[18px]">add</span>
    Nuevo Producto
</a>

{{-- Cancelar / terciario --}}
<a href="{{ route('productos.index') }}"
   class="w-full py-2.5 bg-surface-container-high text-on-surface rounded-xl font-label-lg text-center hover:bg-outline-variant/20 transition-all">
    Cancelar
</a>

{{-- Destructivo (dentro de un <form method="POST"> con @method('DELETE')) --}}
<button class="px-3 py-1 bg-error/50 backdrop-blur-md rounded-lg text-white font-label-sm hover:bg-error/70 transition border border-white/20 flex items-center gap-1">
    <span class="material-symbols-outlined text-sm">delete</span> Eliminar
</button>

{{-- Icon button (header, tabla) --}}
<a href="{{ route('productos.show', $producto) }}" title="Ver detalle"
   class="p-1.5 rounded-lg text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-all">
    <span class="material-symbols-outlined text-[18px]">visibility</span>
</a>
```

## Tarjeta contenedora genérica

```blade
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
    <h3 class="font-label-lg text-on-surface mb-4">Título de sección</h3>
    {{-- contenido --}}
</div>
```

## Tarjeta KPI (dashboard/productos)

```blade
<div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
    <p class="font-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Total Productos</p>
    <div class="flex items-end justify-between">
        <span class="font-headline-md text-headline-md">{{ $totalProductos }}</span>
        <span class="text-primary font-label-sm">+{{ $nuevosEsteMes }} este mes</span>
    </div>
</div>
```

## Tarjeta KPI con ícono circular (categorías/estadísticas)

```blade
<div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl flex items-center gap-4">
    <div class="h-12 w-12 bg-primary/10 text-primary rounded-full flex items-center justify-center">
        <span class="material-symbols-outlined">inventory</span>
    </div>
    <div>
        <p class="text-outline font-label-sm uppercase tracking-wider">Total Productos</p>
        <p class="font-headline-md text-headline-md leading-none">{{ $total }}</p>
    </div>
</div>
```

## Badge de estado

```blade
@php
    $estadoMap = [
        'en_stock'   => ['label' => 'En stock',   'class' => 'bg-tertiary/10 text-tertiary'],
        'bajo_stock' => ['label' => 'Bajo stock', 'class' => 'bg-secondary/10 text-secondary'],
        'agotado'    => ['label' => 'Agotado',    'class' => 'bg-error/10 text-error'],
    ];
    $e = $estadoMap[$producto->estado] ?? ['label' => $producto->estado, 'class' => 'bg-outline/10 text-outline'];
@endphp
<span class="px-2 py-0.5 rounded-full font-label-sm {{ $e['class'] }}">{{ $e['label'] }}</span>
```

## Breadcrumb

```blade
<nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
    <a href="{{ route('productos.index') }}" class="hover:text-primary transition-colors">Inventario</a>
    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
    <span class="text-on-surface">Nuevo Producto</span>
</nav>
```

## Header de página estándar

```blade
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Título de la página</h2>
        <p class="font-body-md text-on-surface-variant">Descripción corta de la sección.</p>
    </div>
    <div class="flex gap-3">
        {{-- botón(es) de acción --}}
    </div>
</div>
```

## Estado vacío (tabla o grid)

```blade
<div class="flex flex-col items-center gap-3 text-outline py-16">
    <span class="material-symbols-outlined text-[48px]">inventory_2</span>
    <p class="font-body-md">No se encontraron productos con los filtros aplicados.</p>
</div>
```

## Input / select / textarea

```blade
<div>
    <label class="font-label-lg text-on-surface-variant block mb-1">Nombre del Producto *</label>
    <input type="text" name="nombre" value="{{ old('nombre') }}" required
           class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
</div>

<select name="categoria_id" required
        class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none">
    <option value="">Seleccionar...</option>
    @foreach($categorias as $cat)
        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
    @endforeach
</select>

<textarea name="descripcion" rows="3"
          class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none">{{ old('descripcion') }}</textarea>
```

Bloque de errores de validación (al final del formulario, no por campo, en
formularios largos con múltiples secciones):

```blade
@if($errors->any())
<div class="p-4 bg-error-container/20 border border-error/20 rounded-xl">
    <ul class="font-body-sm text-error space-y-1">
        @foreach($errors->all() as $error)
            <li>• {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
```

## Formulario en dos columnas (create/edit)

Estructura real de `productos/create.blade.php`: `lg:col-span-2` para los
campos, columna angosta a la derecha para imagen/opciones/submit.

```blade
<form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- tarjetas de campos --}}
        </div>
        <div class="space-y-6">
            {{-- imagen, opciones, botón submit + cancelar --}}
        </div>
    </div>
</form>
```

## Tabla con paginación y pie de totales

```blade
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low">
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Columna</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
                @forelse($items as $item)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-4 py-3">{{ $item->campo }}</td>
                </tr>
                @empty
                <tr><td class="px-4 py-16 text-center"><!-- estado vacío --></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->count())
    <div class="px-4 py-3 border-t border-outline-variant bg-surface-container-low flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-wrap gap-6 font-label-sm text-on-surface-variant">
            <span><span class="text-on-surface font-bold">{{ $items->total() }}</span> resultados</span>
        </div>
        <div>{{ $items->links() }}</div>
    </div>
    @endif
</div>
```

## Grid "bento" de tarjetas grandes (categorías)

Usa spans/heights/overlays rotando por índice para variar el layout sin
JS — ver `categorias/index.blade.php` completo para la versión con imagen de
fondo, overlay de gradiente y acciones flotantes. Esqueleto mínimo:

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6">
    @foreach($categorias as $categoria)
    <div class="lg:col-span-6 group relative overflow-hidden rounded-3xl bg-surface-container-lowest border border-outline-variant shadow-sm transition-all duration-300 hover:shadow-2xl hover:scale-[1.015]">
        <a href="{{ route('categorias.show', $categoria) }}" class="absolute inset-0 z-10"></a>
        {{-- contenido superpuesto --}}
    </div>
    @endforeach
</div>
```

## Filtros GET en formulario horizontal

```blade
<form method="GET" action="{{ route('productos.index') }}"
      class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="font-label-sm text-on-surface-variant block mb-1">Buscar</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="w-full pl-9 pr-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>
        <button type="submit" class="px-5 py-2 bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 active:scale-95 transition-all">Filtrar</button>
        @if(request()->hasAny(['q']))
        <a href="{{ route('productos.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-label-lg hover:bg-outline-variant/20 transition-all">Limpiar</a>
        @endif
    </div>
</form>
```

## JS vanilla via @push('scripts') (sin Livewire)

Patrón real para interactividad simple (select cascada categoría→subcategoría
en `productos/create.blade.php`). El layout `x-layouts.app` renderiza
`@stack('scripts')` justo antes de `</body>`.

```blade
@push('scripts')
<script>
(function () {
    const catSelect = document.getElementById('categoria_id');
    const subSelect = document.getElementById('subcategoria_id');
    catSelect.addEventListener('change', () => {
        // lógica de filtrado
    });
})();
</script>
@endpush
```

## Chart.js (reportes)

Ya cargado vía CDN en `reportes/index.blade.php`
(`<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js">`).
Para un gráfico nuevo, sigue el mismo patrón: canvas con id único +
`new Chart(document.getElementById('miId'), { ... })` dentro de
`@push('scripts')`, usando los tokens de color reales (no defaults de
Chart.js) para las series — p.ej. `#24389c` (primary) para la serie
principal.

## Formulario de login/auth ya migrado (referencia canónica)

Ver `resources/views/livewire/auth/login.blade.php` completo. Puntos clave a
replicar en cualquier vista de auth que migres desde Flux:

```blade
<label class="block text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-2">Correo electrónico</label>
<input wire:model="email" type="email" required
       class="block w-full appearance-none bg-zinc-950 text-zinc-100 text-sm border rounded-xl px-4 py-3 placeholder:text-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all {{ $errors->has('email') ? 'border-red-500/50' : 'border-zinc-700' }}" />

<button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-semibold py-3.5 rounded-xl transition-all duration-200 text-sm tracking-wide shadow-lg shadow-indigo-900/25 cursor-pointer">
    <span wire:loading.remove wire:target="login">Iniciar sesión</span>
    <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
        {{-- spinner SVG --}}
        Verificando...
    </span>
</button>
```

Nota: esta vista usa una paleta oscura (`zinc-950`, `indigo-600`) distinta a la
paleta MD3 clara del resto de la app — es intencional, exclusiva del layout de
auth (`x-layouts.auth.simple`). No mezcles `zinc`/`indigo` en vistas que usan
`x-layouts.app`.
