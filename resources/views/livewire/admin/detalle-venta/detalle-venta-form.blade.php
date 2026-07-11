<div>
    <flux:modal wire:model="mostrarModal" class="max-w-lg">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $detalleVentaId ? 'Editar Línea' : 'Nueva Línea' }}</flux:heading>
                <flux:subheading>Detalle de un producto vendido dentro de una boleta.</flux:subheading>
            </div>

            <flux:select wire:model="ventaId" label="Venta" placeholder="Seleccionar boleta..." required>
                @foreach($ventas as $venta)
                    <flux:select.option value="{{ $venta->id }}">{{ $venta->numero_boleta }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="productoId" label="Producto" placeholder="Seleccionar producto..." required>
                @foreach($productos as $producto)
                    <flux:select.option value="{{ $producto->id }}">{{ $producto->nombre }} ({{ $producto->sku }})</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="cantidad" label="Cantidad" type="number" min="1" required />
                <flux:input wire:model="precioUnitario" label="Precio Unitario (S/)" type="number" step="0.01" min="0" required />
            </div>

            <flux:input wire:model="adicional" label="Adicional (S/)" type="number" step="0.01" min="0" />

            <div class="flex flex-col gap-3">
                <flux:button type="submit" variant="primary">
                    {{ $detalleVentaId ? 'Guardar Cambios' : 'Guardar Línea' }}
                </flux:button>
                <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
