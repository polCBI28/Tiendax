<div>
    <flux:modal wire:model="mostrarModal" class="max-w-2xl">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">Registrar Movimiento</flux:heading>
                <flux:subheading>Ajusta el stock manualmente (entrada o salida).</flux:subheading>
            </div>

            {{-- Tipo --}}
            <div class="grid grid-cols-2 gap-3">
                <button type="button" wire:click="$set('tipo', 'entrada')"
                        class="p-4 rounded-xl border-2 flex items-center gap-3 transition-all {{ $tipo === 'entrada' ? 'border-emerald-500 bg-emerald-500/10' : 'border-zinc-200 dark:border-white/10' }}">
                    <flux:icon.arrow-down-circle class="size-6 text-emerald-600 dark:text-emerald-400" />
                    <div class="text-left">
                        <p class="font-medium text-zinc-800 dark:text-white">Entrada</p>
                        <p class="text-xs text-zinc-400">Aumenta el stock</p>
                    </div>
                </button>
                <button type="button" wire:click="$set('tipo', 'salida')"
                        class="p-4 rounded-xl border-2 flex items-center gap-3 transition-all {{ $tipo === 'salida' ? 'border-red-500 bg-red-500/10' : 'border-zinc-200 dark:border-white/10' }}">
                    <flux:icon.arrow-up-circle class="size-6 text-red-600 dark:text-red-400" />
                    <div class="text-left">
                        <p class="font-medium text-zinc-800 dark:text-white">Salida</p>
                        <p class="text-xs text-zinc-400">Reduce el stock</p>
                    </div>
                </button>
            </div>

            {{-- Cascada Categoría → Subcategoría → Producto --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <flux:select wire:model.live="categoriaId" label="Categoría" placeholder="Seleccionar...">
                    @foreach($categorias as $cat)
                        <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="subcategoriaId" label="Subcategoría" placeholder="Todas" :disabled="! $categoriaId">
                    @foreach($this->subcategoriasDisponibles as $sub)
                        <flux:select.option value="{{ $sub->id }}">{{ $sub->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="productoId" label="Producto" placeholder="Seleccionar..." :disabled="! $categoriaId">
                    @foreach($this->productosDisponibles as $prod)
                        <flux:select.option value="{{ $prod->id }}">{{ $prod->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            @error('productoId') <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text> @enderror

            @if($this->productoSeleccionado)
                <flux:callout icon="cube" variant="secondary"
                    heading="Stock actual: {{ $this->productoSeleccionado->stock }} unidades (mínimo: {{ $this->productoSeleccionado->stock_minimo }})" />
            @endif

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="cantidad" label="Cantidad" type="number" min="1" required />
                <flux:input wire:model="fecha" label="Fecha" type="date" :max="now()->format('Y-m-d')" required />
            </div>
            @error('cantidad') <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text> @enderror

            <flux:input wire:model="motivo" label="Motivo (opcional)" placeholder="Ej. Ajuste de inventario, producto dañado..." />

            <div class="flex flex-col gap-3">
                <flux:button type="submit" variant="primary">Registrar Movimiento</flux:button>
                <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
