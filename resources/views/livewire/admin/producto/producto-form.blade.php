<div>
    <flux:modal wire:model="mostrarModal" class="max-w-3xl">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $productoId ? 'Editar Producto' : 'Nuevo Producto' }}</flux:heading>
                <flux:subheading>Completa la información del producto.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div class="space-y-4">
                        <flux:heading size="sm">Información General</flux:heading>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="nombre" label="Nombre del Producto" required class="col-span-2" />
                            <flux:input wire:model="sku" label="Código" required />
                            <flux:select wire:model="categoriaId" label="Categoría" placeholder="Seleccionar..." required>
                                @foreach($categorias as $cat)
                                    <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:select wire:model="subcategoriaId" label="Subcategoría" placeholder="Ninguna">
                                @foreach($subcategorias as $sub)
                                    <flux:select.option value="{{ $sub->id }}">{{ $sub->nombre }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:textarea wire:model="descripcion" label="Descripción" rows="3" class="col-span-2" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <flux:heading size="sm">Precios y Stock</flux:heading>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="precioVenta" label="Precio de Venta (S/)" type="number" step="0.01" min="0" required />
                            <flux:input wire:model="precioCosto" label="Precio de Costo (S/)" type="number" step="0.01" min="0" />
                            <flux:input wire:model="stock" label="Stock" type="number" min="0" required />
                            <flux:input wire:model="stockMinimo" label="Stock Mínimo" type="number" min="0" required />
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <flux:heading size="sm">Imagen</flux:heading>
                        @if($imagen)
                            <img src="{{ $imagen->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg border border-zinc-200 dark:border-white/10">
                        @elseif($imagenActual)
                            <img src="{{ asset('storage/' . $imagenActual) }}" class="w-full h-32 object-cover rounded-lg border border-zinc-200 dark:border-white/10">
                        @endif
                        <label for="producto-imagen-input" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-lg border border-dashed border-zinc-300 dark:border-white/20 text-sm text-zinc-500 dark:text-zinc-400 cursor-pointer hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all">
                            <flux:icon.arrow-up-tray variant="micro" />
                            <span>{{ $imagen || $imagenActual ? 'Cambiar imagen' : 'Subir imagen' }}</span>
                        </label>
                        <input id="producto-imagen-input" type="file" wire:model="imagen" accept="image/*" class="sr-only">
                        @error('imagen') <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text> @enderror
                    </div>

                    <flux:checkbox wire:model="activo" label="Producto activo" description="El estado se calcula automáticamente según el stock y stock mínimo." />

                    <div class="flex flex-col gap-3">
                        <flux:button type="submit" variant="primary">
                            {{ $productoId ? 'Guardar Cambios' : 'Guardar Producto' }}
                        </flux:button>
                        <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
                    </div>
                </div>

            </div>
        </form>
    </flux:modal>
</div>
