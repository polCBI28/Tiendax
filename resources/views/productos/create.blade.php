<x-layouts.app title="Nuevo Producto">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('productos.index') }}" class="hover:text-primary transition-colors">Inventario</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Nuevo Producto</span>
    </nav>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Nuevo Producto</h2>
</div>

<form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
                <h3 class="font-label-lg text-on-surface mb-4">Información General</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="font-label-lg text-on-surface-variant block mb-1">Nombre del Producto *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Código *</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" required
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Categoría *</label>
                        <select name="categoria_id" id="categoria_id" required
                                class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none">
                            <option value="">Seleccionar...</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Subcategoría</label>
                        <select name="subcategoria_id" id="subcategoria_id"
                                class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">— Selecciona una categoría primero —</option>
                            @foreach($subcategorias as $sub)
                                <option value="{{ $sub->id }}"
                                        data-categoria="{{ $sub->categoria_id }}"
                                        {{ old('subcategoria_id') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="font-label-lg text-on-surface-variant block mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3"
                                  class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
                <h3 class="font-label-lg text-on-surface mb-4">Precios y Stock</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Precio de Venta (S/) *</label>
                        <input type="number" name="precio_venta" value="{{ old('precio_venta') }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Precio de Costo (S/)</label>
                        <input type="number" name="precio_costo" value="{{ old('precio_costo') }}" step="0.01" min="0"
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Stock Inicial *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Stock Mínimo *</label>
                        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 5) }}" min="0" required
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
                <h3 class="font-label-lg text-on-surface mb-4">Imagen</h3>
                <input type="file" name="imagen" accept="image/*"
                       class="w-full font-body-sm text-on-surface-variant file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-container/10 file:text-primary file:font-label-lg hover:file:bg-primary-container/20 transition-all">
            </div>

            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
                <h3 class="font-label-lg text-on-surface mb-4">Opciones</h3>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" id="activo" value="1" checked
                           class="rounded border-outline-variant text-primary focus:ring-primary">
                    <label for="activo" class="font-body-sm text-on-surface">Producto activo</label>
                </div>
                <p class="font-label-sm text-on-surface-variant mt-3">El estado se calcula automáticamente según el stock y stock mínimo.</p>
            </div>

            @if($errors->any())
            <div class="p-4 bg-error-container/20 border border-error/20 rounded-xl">
                <ul class="font-body-sm text-error space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full py-3 bg-primary text-on-primary rounded-xl font-label-lg shadow-sm hover:brightness-110 active:scale-95 transition-all">
                    Guardar Producto
                </button>
                <a href="{{ route('productos.index') }}"
                   class="w-full py-2.5 bg-surface-container-high text-on-surface rounded-xl font-label-lg text-center hover:bg-outline-variant/20 transition-all">
                    Cancelar
                </a>
            </div>
        </div>

    </div>
</form>


@push('scripts')
<script>
(function () {
    const catSelect = document.getElementById('categoria_id');
    const subSelect = document.getElementById('subcategoria_id');
    const subOpciones = [...subSelect.querySelectorAll('option[data-categoria]')];
    const placeholder = subSelect.querySelector('option[value=""]');

    function filtrar(categoriaId) {
        const hayCategoria = !!categoriaId;

        subOpciones.forEach(opt => {
            const pertenece = opt.dataset.categoria === categoriaId;
            opt.hidden    = !pertenece;
            opt.disabled  = !pertenece;
        });

        // Si la opción seleccionada ya no pertenece a la nueva categoría, resetear
        const seleccionada = subSelect.querySelector(`option[value="${subSelect.value}"]`);
        if (subSelect.value && seleccionada && seleccionada.hidden) {
            subSelect.value = '';
        }

        placeholder.textContent = hayCategoria ? 'Ninguna' : '— Selecciona una categoría primero —';
        subSelect.disabled = !hayCategoria;
    }

    catSelect.addEventListener('change', () => filtrar(catSelect.value));

    // Ejecutar al cargar para restaurar old() tras error de validación
    filtrar(catSelect.value);
})();
</script>
@endpush

</x-layouts.app>
