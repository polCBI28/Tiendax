<div
    x-data
    x-on:keydown.window="
        if (! @js($mostrarModal)) return;
        if ($event.key === 'F2') { $event.preventDefault(); $refs.buscador.focus(); }
        if ($event.key === 'F10') { $event.preventDefault(); $wire.guardar('completado'); }
        if ($event.key === 'Escape') { $wire.cerrar(); }
    "
>
    <flux:modal wire:model="mostrarModal" class="max-w-6xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <flux:heading size="lg">Nueva Venta</flux:heading>
                <flux:subheading>Boleta {{ $this->numeroBoletaPreview }} · <kbd class="text-xs">F2</kbd> Buscar · <kbd class="text-xs">F10</kbd> Registrar · <kbd class="text-xs">Esc</kbd> Cancelar</flux:subheading>
            </div>
        </div>

        @error('carrito') <flux:callout icon="exclamation-triangle" variant="danger" heading="{{ $message }}" class="mb-4" /> @enderror

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Panel izquierdo: datos + búsqueda de productos --}}
            <div class="lg:col-span-7 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="fechaVenta" label="Fecha de Venta" type="date" required />
                    <flux:input wire:model="descripcion" label="Descripción (opcional)" placeholder="Ej: Polo azul talla M..." />
                </div>

                <div>
                    <flux:input x-ref="buscador" wire:model.live.debounce.200ms="search" icon="magnifying-glass" placeholder="Buscar por nombre o SKU... (F2)" />
                </div>

                {{-- Tabs de categoría --}}
                <div class="flex gap-1 overflow-x-auto pb-1">
                    <flux:button size="sm" :variant="$categoriaFiltro === null ? 'primary' : 'ghost'" wire:click="filtrarCategoria(null)" class="shrink-0">
                        Todas
                    </flux:button>
                    @foreach($this->categorias as $cat)
                        <flux:button size="sm" :variant="$categoriaFiltro === $cat->id ? 'primary' : 'ghost'" wire:click="filtrarCategoria({{ $cat->id }})" class="shrink-0">
                            {{ $cat->nombre }}
                        </flux:button>
                    @endforeach
                </div>

                {{-- Grid de productos --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-96 overflow-y-auto pr-1" wire:loading.class="opacity-50">
                    @forelse($this->productosDisponibles as $producto)
                        <button type="button" wire:click="agregarProducto({{ $producto->id }})"
                                class="p-3 border border-zinc-200 dark:border-white/10 rounded-xl hover:border-blue-500 hover:shadow-md transition-all text-left flex flex-col">
                            <span class="font-medium text-zinc-800 dark:text-white truncate text-sm">{{ Str::limit($producto->nombre, 20) }}</span>
                            <span class="text-xs text-zinc-400 mt-0.5">Stock: {{ $producto->stock }}</span>
                            <div class="mt-auto flex justify-between items-center pt-2">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">S/ {{ number_format($producto->precio_venta, 2) }}</span>
                                <flux:icon.plus-circle variant="mini" class="text-zinc-400" />
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-8 text-zinc-400">
                            <flux:text>No se encontraron productos.</flux:text>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Panel derecho: carrito --}}
            <div class="lg:col-span-5 flex flex-col bg-zinc-50 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl overflow-hidden">
                <div class="p-4 border-b border-zinc-200 dark:border-white/10 flex items-center justify-between">
                    <div>
                        <flux:heading size="sm">Carrito de Venta</flux:heading>
                        <flux:text size="sm" class="text-zinc-400">{{ count($carrito) }} item{{ count($carrito) === 1 ? '' : 's' }} seleccionado{{ count($carrito) === 1 ? '' : 's' }}</flux:text>
                    </div>
                    @if(count($carrito))
                        <flux:button icon="trash" variant="ghost" size="sm" wire:click="limpiarCarrito" tooltip="Vaciar carrito" />
                    @endif
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-2 max-h-64">
                    @forelse($carrito as $productoId => $item)
                        <div class="p-3 rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-zinc-800 dark:text-white truncate">{{ $item['nombre'] }}</p>
                                    <p class="text-xs text-zinc-400">
                                        S/ {{ number_format($item['precio'] + $item['adicional'], 2) }}/ud
                                        @if($item['adicional'] > 0)
                                            <span class="text-amber-600 dark:text-amber-400">(+S/ {{ number_format($item['adicional'], 2) }} adicional)</span>
                                        @endif
                                    </p>
                                    @if($item['mostrarAdicional'])
                                        <div class="mt-1.5 flex items-center gap-1.5">
                                            <span class="text-xs text-zinc-400">+ Adicional S/</span>
                                            <input type="number" min="0" step="0.5"
                                                   wire:model.live.debounce.300ms="carrito.{{ $productoId }}.adicional"
                                                   class="w-20 text-sm rounded-lg border border-amber-300 dark:border-amber-500/40 px-2 py-1 text-right">
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <flux:button icon="plus-circle" variant="ghost" size="xs" wire:click="toggleAdicional({{ $productoId }})" tooltip="Costo adicional" />
                                    <div class="flex items-center bg-zinc-100 dark:bg-white/10 rounded-lg">
                                        <button type="button" wire:click="cambiarCantidad({{ $productoId }}, -1)" class="px-2 py-1 text-blue-600 dark:text-blue-400">−</button>
                                        <span class="w-6 text-center text-sm">{{ $item['cantidad'] }}</span>
                                        <button type="button" wire:click="cambiarCantidad({{ $productoId }}, 1)" class="px-2 py-1 text-blue-600 dark:text-blue-400">+</button>
                                    </div>
                                    <span class="font-bold text-sm w-16 text-right">S/ {{ number_format(($item['precio'] + $item['adicional']) * $item['cantidad'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-400 text-center py-8">Agrega productos al carrito</p>
                    @endforelse
                </div>

                <div class="p-4 bg-zinc-100 dark:bg-white/5 border-t border-zinc-200 dark:border-white/10 space-y-3">
                    {{-- Descuento --}}
                    <div class="space-y-2">
                        <button type="button" wire:click="$toggle('descuentoActivo')" class="text-sm font-medium text-amber-600 dark:text-amber-400">
                            {{ $descuentoActivo ? '- Ocultar descuento' : '+ Agregar descuento' }}
                        </button>
                        @if($descuentoActivo)
                            <div class="flex items-center gap-2">
                                <flux:button.group>
                                    <flux:button size="sm" :variant="$descuentoTipo === 'monto' ? 'primary' : 'outline'" wire:click="$set('descuentoTipo', 'monto')">S/</flux:button>
                                    <flux:button size="sm" :variant="$descuentoTipo === 'porcentaje' ? 'primary' : 'outline'" wire:click="$set('descuentoTipo', 'porcentaje')">%</flux:button>
                                </flux:button.group>
                                <flux:input wire:model.live="descuentoValor" type="number" min="0" step="0.01" placeholder="0.00" class="flex-1" />
                            </div>
                        @endif
                    </div>

                    {{-- Recargo --}}
                    <div class="space-y-2">
                        <button type="button" wire:click="$toggle('recargoActivo')" class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                            {{ $recargoActivo ? '- Ocultar recargo' : '+ Agregar recargo' }}
                        </button>
                        @if($recargoActivo)
                            <div class="flex items-center gap-2">
                                <flux:button.group>
                                    <flux:button size="sm" :variant="$recargoTipo === 'monto' ? 'primary' : 'outline'" wire:click="$set('recargoTipo', 'monto')">S/</flux:button>
                                    <flux:button size="sm" :variant="$recargoTipo === 'porcentaje' ? 'primary' : 'outline'" wire:click="$set('recargoTipo', 'porcentaje')">%</flux:button>
                                </flux:button.group>
                                <flux:input wire:model.live="recargoValor" type="number" min="0" step="0.01" placeholder="0.00" class="flex-1" />
                            </div>
                        @endif
                    </div>

                    {{-- Totales --}}
                    <div class="space-y-1 pt-2 border-t border-zinc-200 dark:border-white/10">
                        @if($this->totales['descuento'] > 0 || $this->totales['recargo'] > 0)
                            <div class="flex justify-between text-sm text-zinc-500">
                                <span>Subtotal</span>
                                <span>S/ {{ number_format($this->totales['subtotal'], 2) }}</span>
                            </div>
                            @if($this->totales['descuento'] > 0)
                                <div class="flex justify-between text-sm text-amber-600 dark:text-amber-400">
                                    <span>Descuento</span>
                                    <span>- S/ {{ number_format($this->totales['descuento'], 2) }}</span>
                                </div>
                            @endif
                            @if($this->totales['recargo'] > 0)
                                <div class="flex justify-between text-sm text-indigo-600 dark:text-indigo-400">
                                    <span>Recargo</span>
                                    <span>+ S/ {{ number_format($this->totales['recargo'], 2) }}</span>
                                </div>
                            @endif
                        @endif
                        <div class="flex justify-between items-center pt-1">
                            <flux:heading size="sm">Total</flux:heading>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">S/ {{ number_format($this->totales['total'], 2) }}</span>
                        </div>
                    </div>

                    {{-- Adelanto --}}
                    <div class="space-y-2 pt-2 border-t border-zinc-200 dark:border-white/10">
                        <div class="flex items-center justify-between">
                            <flux:text size="sm">Adelanto del cliente</flux:text>
                            <button type="button" wire:click="setAdelanto50" class="text-xs px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg">50%</button>
                        </div>
                        <flux:input wire:model.live="adelanto" type="number" min="0" step="0.01" placeholder="0.00" />
                        @if($this->totales['adelantoAplicado'] > 0 && $this->totales['deuda'] > 0)
                            <div class="flex justify-between text-sm text-red-600 dark:text-red-400">
                                <span>Resta por cobrar</span>
                                <span class="font-bold">S/ {{ number_format($this->totales['deuda'], 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <div class="flex gap-2">
                            <flux:button variant="outline" wire:click="guardar('borrador')" class="flex-1">Guardar Borrador</flux:button>
                            <flux:button variant="ghost" wire:click="cerrar">Cancelar</flux:button>
                        </div>
                        <flux:button variant="primary" wire:click="guardar('completado')" icon="check-circle">
                            Registrar Venta (F10)
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
