<x-layouts.app title="Editar Subcategoría">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('subcategorias.index') }}" class="hover:text-primary transition-colors">Subcategorías</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $subcategoria->nombre }}</span>
    </nav>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Editar Subcategoría</h2>
</div>

<div class="max-w-2xl">
    <form action="{{ route('subcategorias.update', $subcategoria) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 space-y-4">
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Categoría Principal *</label>
                <select name="categoria_id" required
                        class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none">
                    <option value="">Seleccionar...</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ ($subcategoria->categoria_id == $cat->id) ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $subcategoria->nombre) }}" required
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none">{{ old('descripcion', $subcategoria->descripcion) }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="activo" id="activo" value="1"
                       {{ $subcategoria->activo ? 'checked' : '' }}
                       class="rounded border-outline-variant text-primary focus:ring-primary">
                <label for="activo" class="font-body-sm text-on-surface">Subcategoría activa</label>
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
                    class="px-6 py-3 bg-secondary text-on-secondary rounded-xl font-label-lg shadow-sm hover:brightness-110 active:scale-95 transition-all">
                Actualizar Subcategoría
            </button>
            <a href="{{ route('subcategorias.index') }}"
               class="px-6 py-3 bg-surface-container-high text-on-surface rounded-xl font-label-lg hover:bg-outline-variant/20 transition-all">
                Cancelar
            </a>
        </div>
    </form>
</div>

</x-layouts.app>
