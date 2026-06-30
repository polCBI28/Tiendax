<x-layouts.app title="Registrar Movimiento">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('movimientos.index') }}" class="hover:text-primary transition-colors">Movimientos</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Nuevo</span>
    </nav>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Registrar Movimiento de Stock</h2>
    <p class="font-body-md text-on-surface-variant mt-1">Use este formulario para entradas por compra/devolución o salidas por ajuste/merma.</p>
</div>

<div class="max-w-2xl">
    <form action="{{ route('movimientos.store') }}" method="POST">
        @csrf
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 space-y-6">

            {{-- Tipo --}}
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-2">Tipo de Movimiento *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="tipo" value="entrada" class="sr-only peer" {{ old('tipo', 'entrada') == 'entrada' ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-outline-variant peer-checked:border-tertiary peer-checked:bg-tertiary/5 transition-all">
                            <span class="material-symbols-outlined text-tertiary text-[24px]">arrow_downward</span>
                            <div>
                                <p class="font-label-lg text-on-surface">Entrada</p>
                                <p class="font-label-sm text-on-surface-variant">Compra / Devolución</p>
                            </div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="tipo" value="salida" class="sr-only peer" {{ old('tipo') == 'salida' ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-outline-variant peer-checked:border-secondary peer-checked:bg-secondary/5 transition-all">
                            <span class="material-symbols-outlined text-secondary text-[24px]">arrow_upward</span>
                            <div>
                                <p class="font-label-lg text-on-surface">Salida</p>
                                <p class="font-label-sm text-on-surface-variant">Ajuste / Merma</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Selección de Producto en 3 pasos --}}
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-3">Producto *</label>

                {{-- Indicadores de paso --}}
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex items-center gap-1.5">
                        <span id="dot-cat" class="w-5 h-5 rounded-full bg-primary flex items-center justify-center text-[11px] font-bold text-on-primary">1</span>
                        <span class="font-label-sm text-on-surface">Categoría</span>
                    </div>
                    <span class="material-symbols-outlined text-outline text-[16px]">chevron_right</span>
                    <div class="flex items-center gap-1.5">
                        <span id="dot-sub" class="w-5 h-5 rounded-full bg-outline-variant flex items-center justify-center text-[11px] font-bold text-outline">2</span>
                        <span id="lbl-sub" class="font-label-sm text-outline">Subcategoría</span>
                    </div>
                    <span class="material-symbols-outlined text-outline text-[16px]">chevron_right</span>
                    <div class="flex items-center gap-1.5">
                        <span id="dot-prod" class="w-5 h-5 rounded-full bg-outline-variant flex items-center justify-center text-[11px] font-bold text-outline">3</span>
                        <span id="lbl-prod" class="font-label-sm text-outline">Producto</span>
                    </div>
                </div>

                <div class="space-y-3">
                    {{-- Paso 1: Categoría --}}
                    <select id="sel-categoria"
                            class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                        <option value="">Seleccionar categoría...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" data-icono="{{ $cat->icono ?? 'category' }}">
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Paso 2: Subcategoría --}}
                    <div id="wrap-subcategoria" class="hidden">
                        <select id="sel-subcategoria"
                                class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                            <option value="">Todas las subcategorías</option>
                        </select>
                    </div>

                    {{-- Paso 3: Producto (campo real que se envía) --}}
                    <div id="wrap-producto" class="hidden">
                        <select name="producto_id" id="sel-producto" required
                                class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none transition-all">
                            <option value="">Seleccionar producto...</option>
                        </select>
                    </div>
                </div>

                {{-- Info de stock del producto seleccionado --}}
                <div id="stock-info" class="mt-3 hidden p-3 bg-surface-container-low rounded-lg flex items-center gap-3">
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant">info</span>
                    <p class="font-label-sm text-on-surface-variant">
                        Stock actual: <span id="stock-actual" class="font-bold text-on-surface"></span> uds.
                        · Mínimo: <span id="stock-minimo" class="font-bold text-on-surface"></span> uds.
                    </p>
                </div>
            </div>

            {{-- Cantidad --}}
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Cantidad *</label>
                <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" min="1" required
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>

            {{-- Fecha del movimiento --}}
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Fecha del Movimiento *</label>
                <input type="date" name="fecha"
                       value="{{ old('fecha', now()->toDateString()) }}"
                       max="{{ now()->toDateString() }}"
                       required
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                <p class="mt-1 font-label-sm text-outline">Permite registrar movimientos de días anteriores.</p>
            </div>

            {{-- Motivo --}}
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Motivo / Observación</label>
                <input type="text" name="motivo" value="{{ old('motivo') }}" maxlength="255"
                       placeholder="Ej: Compra a proveedor ABC, Merma por caducidad..."
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>

        @if($errors->any())
        <div class="mt-4 p-4 bg-error-container/20 border border-error/20 rounded-xl">
            <ul class="font-body-sm text-error space-y-1">
                @foreach($errors->all() as $error) <li>• {{ $error }}</li> @endforeach
            </ul>
        </div>
        @endif

        <div class="flex gap-3 mt-6">
            <button type="submit"
                    class="px-6 py-3 bg-primary text-on-primary rounded-xl font-label-lg shadow-sm hover:brightness-110 active:scale-95 transition-all">
                Registrar Movimiento
            </button>
            <a href="{{ route('movimientos.index') }}"
               class="px-6 py-3 bg-surface-container-high text-on-surface rounded-xl font-label-lg hover:bg-outline-variant/20 transition-all">
                Cancelar
            </a>
        </div>
    </form>
</div>

@php
    $categoriasJson = $categorias->map(fn($c) => [
        'id'            => $c->id,
        'nombre'        => $c->nombre,
        'subcategorias' => $c->subcategorias->map(fn($s) => ['id' => $s->id, 'nombre' => $s->nombre])->values(),
    ])->values();

    $productosJson = $productos->map(fn($p) => [
        'id'              => $p->id,
        'nombre'          => $p->nombre,
        'sku'             => $p->sku,
        'stock'           => $p->stock,
        'stock_minimo'    => $p->stock_minimo,
        'categoria_id'    => $p->categoria_id,
        'subcategoria_id' => $p->subcategoria_id,
    ])->values();
@endphp

<script>
const categoriasData = {!! json_encode($categoriasJson) !!};
const productosData  = {!! json_encode($productosJson) !!};

const oldProductoId = {{ old('producto_id') ? (int) old('producto_id') : 'null' }};

// Elementos
const selCat    = document.getElementById('sel-categoria');
const selSub    = document.getElementById('sel-subcategoria');
const selProd   = document.getElementById('sel-producto');
const wrapSub   = document.getElementById('wrap-subcategoria');
const wrapProd  = document.getElementById('wrap-producto');
const stockInfo = document.getElementById('stock-info');
const stockEl   = document.getElementById('stock-actual');
const minimoEl  = document.getElementById('stock-minimo');
const dotSub    = document.getElementById('dot-sub');
const dotProd   = document.getElementById('dot-prod');
const lblSub    = document.getElementById('lbl-sub');
const lblProd   = document.getElementById('lbl-prod');

function activarDot(dot, lbl) {
    dot.classList.replace('bg-outline-variant', 'bg-primary');
    dot.classList.replace('text-outline', 'text-on-primary');
    lbl.classList.replace('text-outline', 'text-on-surface');
}
function desactivarDot(dot, lbl) {
    dot.classList.replace('bg-primary', 'bg-outline-variant');
    dot.classList.replace('text-on-primary', 'text-outline');
    lbl.classList.replace('text-on-surface', 'text-outline');
}

function poblarSubcategorias(categoriaId) {
    const cat = categoriasData.find(c => c.id == categoriaId);
    selSub.innerHTML = '<option value="">Todas las subcategorías</option>';
    if (cat && cat.subcategorias.length) {
        cat.subcategorias.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.nombre;
            selSub.appendChild(opt);
        });
        wrapSub.classList.remove('hidden');
        activarDot(dotSub, lblSub);
    } else {
        wrapSub.classList.add('hidden');
        desactivarDot(dotSub, lblSub);
    }
}

function poblarProductos(categoriaId, subcategoriaId) {
    let lista = productosData.filter(p => p.categoria_id == categoriaId);
    if (subcategoriaId) {
        lista = lista.filter(p => p.subcategoria_id == subcategoriaId);
    }
    selProd.innerHTML = '<option value="">Seleccionar producto...</option>';
    lista.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.nombre} — Stock: ${p.stock} uds.`;
        opt.dataset.stock   = p.stock;
        opt.dataset.minimo  = p.stock_minimo;
        selProd.appendChild(opt);
    });
    wrapProd.classList.remove('hidden');
    activarDot(dotProd, lblProd);
    stockInfo.classList.add('hidden');
}

selCat.addEventListener('change', function () {
    const catId = this.value;
    selProd.innerHTML = '<option value="">Seleccionar producto...</option>';
    wrapProd.classList.add('hidden');
    desactivarDot(dotProd, lblProd);
    stockInfo.classList.add('hidden');
    if (!catId) {
        wrapSub.classList.add('hidden');
        desactivarDot(dotSub, lblSub);
        return;
    }
    poblarSubcategorias(catId);
    poblarProductos(catId, '');
});

selSub.addEventListener('change', function () {
    const catId = selCat.value;
    if (!catId) return;
    poblarProductos(catId, this.value);
});

selProd.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (this.value && opt.dataset.stock !== undefined) {
        stockEl.textContent   = opt.dataset.stock;
        minimoEl.textContent  = opt.dataset.minimo;
        stockInfo.classList.remove('hidden');
    } else {
        stockInfo.classList.add('hidden');
    }
});

// Restaurar selección previa (después de error de validación)
if (oldProductoId) {
    const prod = productosData.find(p => p.id === oldProductoId);
    if (prod) {
        selCat.value = prod.categoria_id;
        poblarSubcategorias(prod.categoria_id);
        if (prod.subcategoria_id) {
            selSub.value = prod.subcategoria_id;
        }
        poblarProductos(prod.categoria_id, prod.subcategoria_id || '');
        selProd.value = oldProductoId;
        selProd.dispatchEvent(new Event('change'));
    }
}
</script>

</x-layouts.app>
