<x-layouts.app title="Nueva Categoría">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('categorias.index') }}" class="hover:text-primary transition-colors">Catálogo</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Nueva Categoría</span>
    </nav>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Nueva Categoría</h2>
</div>

@php
$iconos = [
    'Ropa y moda'    => ['checkroom','dry_cleaning','style','apparel','laundry','diamond','watch','king_bed'],
    'Alimentos'      => ['restaurant','local_cafe','lunch_dining','fastfood','local_pizza','bakery_dining','icecream','liquor'],
    'Tecnología'     => ['devices','smartphone','laptop','headphones','keyboard','mouse','tablet','monitor'],
    'Hogar'          => ['home','chair','bed','kitchen','cleaning_services','outdoor_grill','light','bathtub'],
    'Salud y belleza'=> ['spa','face','medical_services','local_pharmacy','fitness_center','self_improvement','health_and_beauty','sanitizer'],
    'Deportes'       => ['sports_soccer','sports_basketball','sports_tennis','directions_bike','pool','hiking','sports_esports','skateboarding'],
    'Juguetes'       => ['toys','child_care','sports_esports','casino','palette','music_note','movie','photo_camera'],
    'Otros'          => ['category','sell','local_offer','redeem','card_giftcard','inventory_2','shopping_bag','store'],
];
$selectedIcon = old('icono', 'category');
@endphp

<form action="{{ route('categorias.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
                <h3 class="font-label-lg text-on-surface mb-4">Información</h3>
                <div class="space-y-4">
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="font-label-lg text-on-surface-variant block mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3"
                                  class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Icon Picker --}}
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
                <h3 class="font-label-lg text-on-surface mb-1">Ícono de la categoría</h3>
                <p class="font-label-sm text-on-surface-variant mb-4">Se muestra en las tarjetas del catálogo.</p>

                <input type="hidden" name="icono" id="icono-input" value="{{ $selectedIcon }}">

                {{-- Preview + búsqueda --}}
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center shrink-0 border-2 border-primary/20">
                        <span class="material-symbols-outlined text-primary text-[36px]" id="icono-preview">{{ $selectedIcon }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-label-sm text-on-surface-variant mb-1">Ícono seleccionado</p>
                        <p class="font-label-lg text-on-surface" id="icono-nombre">{{ $selectedIcon }}</p>
                    </div>
                    <div class="flex-1">
                        <input type="text" id="icono-buscar" placeholder="Buscar ícono..."
                               class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                </div>

                {{-- Grid de iconos por sección --}}
                <div id="icono-grid" class="max-h-72 overflow-y-auto space-y-4 pr-1">
                    @foreach($iconos as $seccion => $lista)
                    <div class="icono-seccion">
                        <p class="font-label-sm text-outline uppercase tracking-wider mb-2">{{ $seccion }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($lista as $icon)
                            <button type="button"
                                    data-icon="{{ $icon }}"
                                    title="{{ $icon }}"
                                    class="icono-btn w-11 h-11 flex items-center justify-center rounded-xl border transition-all hover:border-primary hover:bg-primary/10
                                           {{ $selectedIcon === $icon ? 'border-primary bg-primary/10 text-primary' : 'border-outline-variant bg-surface-container-low text-on-surface-variant' }}">
                                <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
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
                <h3 class="font-label-lg text-on-surface mb-3">Estado</h3>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" id="activo" value="1" checked
                           class="rounded border-outline-variant text-primary focus:ring-primary">
                    <label for="activo" class="font-body-sm text-on-surface">Categoría activa</label>
                </div>
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
                    Guardar Categoría
                </button>
                <a href="{{ route('categorias.index') }}"
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
    const input    = document.getElementById('icono-input');
    const preview  = document.getElementById('icono-preview');
    const nombre   = document.getElementById('icono-nombre');
    const buscar   = document.getElementById('icono-buscar');
    const botones  = document.querySelectorAll('.icono-btn');

    botones.forEach(btn => {
        btn.addEventListener('click', () => {
            const icon = btn.dataset.icon;
            input.value   = icon;
            preview.textContent = icon;
            nombre.textContent  = icon;
            botones.forEach(b => {
                b.classList.remove('border-primary', 'bg-primary/10', 'text-primary');
                b.classList.add('border-outline-variant', 'bg-surface-container-low', 'text-on-surface-variant');
            });
            btn.classList.add('border-primary', 'bg-primary/10', 'text-primary');
            btn.classList.remove('border-outline-variant', 'bg-surface-container-low', 'text-on-surface-variant');
        });
    });

    buscar.addEventListener('input', () => {
        const q = buscar.value.toLowerCase().trim();
        document.querySelectorAll('.icono-seccion').forEach(seccion => {
            let hayVisible = false;
            seccion.querySelectorAll('.icono-btn').forEach(btn => {
                const coincide = btn.dataset.icon.includes(q);
                btn.style.display = coincide ? '' : 'none';
                if (coincide) hayVisible = true;
            });
            seccion.style.display = hayVisible ? '' : 'none';
        });
    });
})();
</script>
@endpush

</x-layouts.app>
