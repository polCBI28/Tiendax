<x-layouts.sales>

<div class="grid grid-cols-12 gap-6 p-6 h-full overflow-hidden">

    {{-- Panel izquierdo: búsqueda de productos --}}
    <div class="col-span-7 flex flex-col space-y-4 overflow-y-auto no-scrollbar pb-6">

        {{-- Datos del Comprobante --}}
        <section class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm">
            <h3 class="font-headline-md text-headline-md text-primary flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined">description</span>
                Datos del Comprobante
            </h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="font-label-lg text-on-surface-variant">N° Recibo / Boleta</label>
                    <input type="text" id="numero_boleta" readonly
                           value="B001-{{ str_pad(\App\Models\Venta::count() + 1, 6, '0', STR_PAD_LEFT) }}"
                           class="w-full bg-surface-container-low border border-outline-variant rounded-lg py-2 px-3 font-body-sm text-on-surface-variant outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-label-lg text-on-surface-variant">Fecha de Venta</label>
                    <input type="date" id="fecha_venta" value="{{ date('Y-m-d') }}"
                           class="w-full bg-white border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-lg py-2 px-3 font-body-sm transition-all outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-label-lg text-on-surface-variant">Cliente (Opcional)</label>
                    <select id="cliente_id"
                            class="w-full bg-white border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-lg py-2 px-3 font-body-sm transition-all outline-none">
                        <option value="">— Sin cliente —</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- Búsqueda Rápida --}}
        <section class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm flex-1 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-headline-md text-headline-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined">search</span>
                    Búsqueda Rápida de Productos
                </h3>
                <span class="font-label-sm text-outline px-2 py-1 bg-surface-container-low rounded border border-outline-variant">
                    [F2] Buscar | [Enter] Agregar
                </span>
            </div>

            <div class="relative mb-4">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-outline">
                    <span class="material-symbols-outlined">barcode_scanner</span>
                </span>
                <input id="buscar-producto" autofocus type="text"
                       placeholder="Buscar por nombre, código o categoría..."
                       class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-2 border-transparent focus:border-primary focus:bg-white rounded-xl shadow-inner transition-all outline-none font-body-md">
            </div>

            {{-- Tabs por categoría --}}
            <div class="flex gap-1 border-b border-outline-variant mb-4 overflow-x-auto no-scrollbar">
                <button onclick="filtrarCategoria('')"
                        class="tab-btn shrink-0 px-4 py-2 border-b-2 border-primary text-primary font-label-lg transition-all">
                    Frecuentes
                </button>
                @foreach($categorias as $cat)
                <button onclick="filtrarCategoria('{{ $cat->id }}')"
                        class="tab-btn shrink-0 px-4 py-2 border-b-2 border-transparent text-on-surface-variant hover:text-primary font-label-lg transition-all">
                    {{ $cat->nombre }}
                </button>
                @endforeach
            </div>

            {{-- Grid de productos --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 overflow-y-auto no-scrollbar flex-1" id="grid-productos">
                @foreach($productos as $producto)
                <button class="producto-card group p-3 border border-outline-variant rounded-xl hover:border-primary hover:shadow-md transition-all text-left flex flex-col"
                        data-id="{{ $producto->id }}"
                        data-nombre="{{ $producto->nombre }}"
                        data-precio="{{ $producto->precio_venta }}"
                        data-stock="{{ $producto->stock }}"
                        data-categoria="{{ $producto->categoria_id }}"
                        onclick="agregarAlCarrito(this)">
                    <span class="font-label-lg text-on-surface truncate">{{ Str::limit($producto->nombre, 16) }}</span>
                    <span class="font-label-sm text-on-surface-variant mt-1">Stock: {{ $producto->stock }} und.</span>
                    <div class="mt-auto flex justify-between items-center pt-2">
                        <span class="font-mono-data text-primary font-bold">S/ {{ $producto->precio_venta }}</span>
                        <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">add_circle</span>
                    </div>
                </button>
                @endforeach
            </div>
        </section>
    </div>

    {{-- Panel derecho: Carrito --}}
    <div class="col-span-5 flex flex-col h-full bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg overflow-hidden">

        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
            <div>
                <h3 class="font-headline-md text-headline-md text-primary">Carrito de Ventas</h3>
                <p id="items-count" class="font-label-sm text-on-surface-variant">0 items seleccionados</p>
            </div>
            <button onclick="limpiarCarrito()" class="text-on-surface-variant hover:text-error hover:bg-error-container/20 p-2 rounded-lg transition-all">
                <span class="material-symbols-outlined">delete_sweep</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-3 no-scrollbar" id="carrito-items">
            <p class="font-label-sm text-on-surface-variant text-center py-8" id="carrito-vacio">
                Agrega productos al carrito
            </p>
        </div>

        <div class="p-6 bg-surface-container border-t border-outline-variant space-y-4">
            {{-- Descuento --}}
            <div class="space-y-2">
                <button onclick="toggleDescuento()" type="button" id="btn-descuento"
                        class="flex items-center gap-2 font-label-lg text-secondary hover:text-secondary/80 transition-colors">
                    <span id="label-descuento">+ Agregar descuento</span>
                </button>
                <div id="panel-descuento" class="hidden">
                    <div class="flex items-center gap-2 p-3 bg-secondary/5 rounded-xl border border-secondary/15">
                        <div class="flex bg-white rounded-lg border border-outline-variant overflow-hidden shrink-0">
                            <button type="button" onclick="setTipoDescuento('monto')" id="btn-tipo-monto"
                                    class="px-3 py-1.5 font-label-sm transition-all bg-secondary text-on-secondary">
                                S/
                            </button>
                            <button type="button" onclick="setTipoDescuento('porcentaje')" id="btn-tipo-porcentaje"
                                    class="px-3 py-1.5 font-label-sm transition-all text-on-surface-variant hover:bg-surface-container-low">
                                %
                            </button>
                        </div>
                        <input type="number" id="descuento-input" min="0" step="0.01" placeholder="0.00"
                               oninput="actualizarTotales()"
                               class="flex-1 bg-white border border-outline-variant rounded-lg py-1.5 px-3 font-mono-data text-on-surface text-right focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all">
                        <button onclick="quitarDescuento()" type="button"
                                class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error/10 rounded-lg transition-all shrink-0">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Totales --}}
            <div class="space-y-2">
                <div class="flex justify-between items-center font-label-lg text-on-surface-variant" id="fila-subtotal" style="display:none">
                    <span>Subtotal</span>
                    <span class="font-mono-data" id="subtotal-display">S/ 0.00</span>
                </div>
                <div class="flex justify-between items-center font-label-lg text-secondary" id="fila-descuento" style="display:none">
                    <span id="descuento-label">Descuento</span>
                    <span class="font-mono-data" id="descuento-display">- S/ 0.00</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-headline-md text-on-surface">Total</span>
                    <span class="text-[32px] font-bold text-primary font-mono-data" id="total">S/ 0.00</span>
                </div>
            </div>

            <div class="flex flex-col space-y-3 pt-1">
                <div class="flex space-x-3">
                    <button onclick="guardarVenta('borrador')"
                            class="flex-1 py-3 border-2 border-primary text-primary font-label-lg rounded-xl hover:bg-primary/5 active:scale-95 transition-all">
                        Guardar Borrador
                    </button>
                    <button class="p-3 bg-secondary-fixed text-on-secondary-fixed rounded-xl hover:bg-secondary-fixed-dim active:scale-95 transition-all">
                        <span class="material-symbols-outlined">print</span>
                    </button>
                </div>
                <button id="registerSaleBtn" onclick="guardarVenta('completado')"
                        class="w-full py-5 bg-primary text-on-primary font-headline-md rounded-xl shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    REGISTRAR VENTA (F10)
                </button>
                <a href="{{ route('ventas.index') }}"
                   class="text-center font-label-sm text-on-surface-variant hover:text-on-surface transition-colors py-1">
                    ESC — Cancelar y volver
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Form oculto para submit --}}
<form id="form-venta" action="{{ route('ventas.store') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="fecha_venta" id="input-fecha">
    <input type="hidden" name="cliente_id" id="input-cliente">
    <input type="hidden" name="estado" id="input-estado">
    <input type="hidden" name="descuento_tipo" id="input-descuento-tipo">
    <input type="hidden" name="descuento_valor" id="input-descuento-valor">
    <div id="input-productos"></div>
</form>

<script>
let carrito = [];
let descuentoTipo = 'monto';
let descuentoActivo = false;

function agregarAlCarrito(el) {
    const id     = el.dataset.id;
    const nombre = el.dataset.nombre;
    const precio = parseFloat(el.dataset.precio);
    const stock  = parseInt(el.dataset.stock);

    const existente = carrito.find(i => i.id == id);
    if (existente) {
        if (existente.cantidad < stock) existente.cantidad++;
    } else {
        carrito.push({ id, nombre, precio, stock, cantidad: 1 });
    }
    el.classList.add('active-row');
    setTimeout(() => el.classList.remove('active-row'), 300);
    renderCarrito();
}

function cambiarCantidad(id, delta) {
    const item = carrito.find(i => i.id == id);
    if (!item) return;
    item.cantidad += delta;
    if (item.cantidad <= 0) carrito = carrito.filter(i => i.id != id);
    renderCarrito();
}

function limpiarCarrito() {
    carrito = [];
    quitarDescuento();
    renderCarrito();
}

function renderCarrito() {
    const container = document.getElementById('carrito-items');
    const count     = document.getElementById('items-count');

    if (carrito.length === 0) {
        container.innerHTML = '<p class="font-label-sm text-on-surface-variant text-center py-8">Agrega productos al carrito</p>';
        count.textContent = '0 items seleccionados';
    } else {
        count.textContent = carrito.length + ' item' + (carrito.length > 1 ? 's' : '') + ' seleccionado' + (carrito.length > 1 ? 's' : '');
        container.innerHTML = carrito.map(item => `
            <div class="flex items-center justify-between p-3 rounded-lg border border-outline-variant bg-white group">
                <div class="flex-1 min-w-0">
                    <p class="font-label-lg text-on-surface truncate">${item.nombre}</p>
                    <p class="font-label-sm text-on-surface-variant">P. Unit: S/ ${item.precio.toFixed(2)}</p>
                </div>
                <div class="flex items-center gap-3 ml-3">
                    <div class="flex items-center bg-surface-container-low rounded-lg border border-outline-variant">
                        <button onclick="cambiarCantidad('${item.id}', -1)" class="px-2 py-1 text-primary hover:bg-primary/10 rounded-l-lg transition-colors">−</button>
                        <span class="w-8 text-center font-mono-data text-on-surface">${item.cantidad}</span>
                        <button onclick="cambiarCantidad('${item.id}', 1)" class="px-2 py-1 text-primary hover:bg-primary/10 rounded-r-lg transition-colors">+</button>
                    </div>
                    <div class="text-right w-20">
                        <p class="font-mono-data font-bold text-on-surface">S/ ${(item.precio * item.cantidad).toFixed(2)}</p>
                    </div>
                </div>
            </div>
        `).join('');
    }
    actualizarTotales();
}

// Descuento
function toggleDescuento() {
    descuentoActivo = !descuentoActivo;
    document.getElementById('panel-descuento').classList.toggle('hidden', !descuentoActivo);
    document.getElementById('label-descuento').textContent = descuentoActivo ? '- Ocultar descuento' : '+ Agregar descuento';
    if (descuentoActivo) document.getElementById('descuento-input').focus();
    actualizarTotales();
}

function quitarDescuento() {
    descuentoActivo = false;
    document.getElementById('descuento-input').value = '';
    document.getElementById('panel-descuento').classList.add('hidden');
    document.getElementById('label-descuento').textContent = '+ Agregar descuento';
    actualizarTotales();
}

function setTipoDescuento(tipo) {
    descuentoTipo = tipo;
    const btnMonto = document.getElementById('btn-tipo-monto');
    const btnPorcentaje = document.getElementById('btn-tipo-porcentaje');

    if (tipo === 'monto') {
        btnMonto.className = 'px-3 py-1.5 font-label-sm transition-all bg-secondary text-on-secondary';
        btnPorcentaje.className = 'px-3 py-1.5 font-label-sm transition-all text-on-surface-variant hover:bg-surface-container-low';
    } else {
        btnPorcentaje.className = 'px-3 py-1.5 font-label-sm transition-all bg-secondary text-on-secondary';
        btnMonto.className = 'px-3 py-1.5 font-label-sm transition-all text-on-surface-variant hover:bg-surface-container-low';
    }
    actualizarTotales();
}

function calcularDescuento(subtotal) {
    if (!descuentoActivo) return 0;
    const val = parseFloat(document.getElementById('descuento-input').value) || 0;
    if (val <= 0) return 0;
    if (descuentoTipo === 'porcentaje') {
        return Math.round(subtotal * Math.min(val, 100) / 100 * 100) / 100;
    }
    return Math.min(val, subtotal);
}

function actualizarTotales() {
    const subtotal = carrito.reduce((sum, i) => sum + i.precio * i.cantidad, 0);
    const descuento = calcularDescuento(subtotal);
    const total = subtotal - descuento;
    const hayDescuento = descuento > 0;

    document.getElementById('fila-subtotal').style.display = hayDescuento ? 'flex' : 'none';
    document.getElementById('fila-descuento').style.display = hayDescuento ? 'flex' : 'none';
    document.getElementById('subtotal-display').textContent = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('descuento-display').textContent = '- S/ ' + descuento.toFixed(2);

    const val = parseFloat(document.getElementById('descuento-input').value) || 0;
    if (descuentoTipo === 'porcentaje' && val > 0) {
        document.getElementById('descuento-label').textContent = 'Descuento (' + Math.min(val, 100) + '%)';
    } else {
        document.getElementById('descuento-label').textContent = 'Descuento';
    }

    document.getElementById('total').textContent = 'S/ ' + total.toFixed(2);
}

function guardarVenta(estado) {
    if (carrito.length === 0) { alert('Agrega al menos un producto al carrito.'); return; }

    const subtotal = carrito.reduce((sum, i) => sum + i.precio * i.cantidad, 0);
    const descuento = calcularDescuento(subtotal);
    const descuentoVal = parseFloat(document.getElementById('descuento-input').value) || 0;

    document.getElementById('input-fecha').value   = document.getElementById('fecha_venta').value;
    document.getElementById('input-cliente').value  = document.getElementById('cliente_id').value;
    document.getElementById('input-estado').value   = estado;
    document.getElementById('input-descuento-tipo').value  = (descuentoActivo && descuentoVal > 0) ? descuentoTipo : '';
    document.getElementById('input-descuento-valor').value = (descuentoActivo && descuentoVal > 0) ? descuentoVal : 0;

    const container = document.getElementById('input-productos');
    container.innerHTML = '';
    carrito.forEach((item, i) => {
        container.innerHTML += `
            <input type="hidden" name="productos[${i}][producto_id]"    value="${item.id}">
            <input type="hidden" name="productos[${i}][cantidad]"        value="${item.cantidad}">
            <input type="hidden" name="productos[${i}][precio_unitario]" value="${item.precio}">
        `;
    });

    const btn = document.getElementById('registerSaleBtn');
    btn.innerHTML = '<span class="material-symbols-outlined">sync</span><span>Procesando...</span>';
    btn.disabled = true;
    document.getElementById('form-venta').submit();
}

// Búsqueda en tiempo real
document.getElementById('buscar-producto').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.producto-card').forEach(card => {
        card.style.display = card.dataset.nombre.toLowerCase().includes(q) ? '' : 'none';
    });
});

// Filtro por categoría
function filtrarCategoria(catId) {
    document.querySelectorAll('.producto-card').forEach(card => {
        card.style.display = (!catId || card.dataset.categoria == catId) ? '' : 'none';
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-primary', 'text-primary');
        btn.classList.add('border-transparent', 'text-on-surface-variant');
    });
    event.target.classList.add('border-primary', 'text-primary');
    event.target.classList.remove('border-transparent', 'text-on-surface-variant');
}

// Atajos de teclado
document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') { e.preventDefault(); document.getElementById('buscar-producto').focus(); }
    if (e.key === 'F10') { e.preventDefault(); guardarVenta('completado'); }
    if (e.key === 'Escape') { window.location.href = '{{ route("ventas.index") }}'; }
});
</script>

</x-layouts.sales>
