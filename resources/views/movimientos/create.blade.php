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
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 space-y-5">

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

            {{-- Producto --}}
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Producto *</label>
                <select name="producto_id" required id="producto-select"
                        class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary outline-none">
                    <option value="">Seleccionar producto...</option>
                    @foreach($productos as $p)
                        <option value="{{ $p->id }}"
                                data-stock="{{ $p->stock }}"
                                data-minimo="{{ $p->stock_minimo }}"
                                {{ old('producto_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre }} — Stock actual: {{ $p->stock }} uds.
                        </option>
                    @endforeach
                </select>
                <div id="stock-info" class="mt-2 hidden p-3 bg-surface-container-low rounded-lg flex items-center gap-3">
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant">info</span>
                    <p class="font-label-sm text-on-surface-variant">Stock actual: <span id="stock-actual" class="font-bold text-on-surface"></span> uds. · Mínimo: <span id="stock-minimo"></span> uds.</p>
                </div>
            </div>

            {{-- Cantidad --}}
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Cantidad *</label>
                <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" min="1" required
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
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

<script>
    const select = document.getElementById('producto-select');
    const infoBox = document.getElementById('stock-info');
    const stockEl = document.getElementById('stock-actual');
    const minimoEl = document.getElementById('stock-minimo');

    select.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const stock = opt.dataset.stock;
        if (this.value && stock !== undefined) {
            stockEl.textContent = stock;
            minimoEl.textContent = opt.dataset.minimo;
            infoBox.classList.remove('hidden');
        } else {
            infoBox.classList.add('hidden');
        }
    });
</script>

</x-layouts.app>
