<div>
    <flux:modal wire:model="mostrarModal" class="max-w-lg">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $subcategoriaId ? 'Editar Subcategoría' : 'Nueva Subcategoría' }}</flux:heading>
                <flux:subheading>Completa la información de la subcategoría.</flux:subheading>
            </div>

            <flux:select wire:model="categoriaId" label="Categoría Principal" placeholder="Seleccionar..." required>
                @foreach($categorias as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="nombre" label="Nombre" required />
            <flux:textarea wire:model="descripcion" label="Descripción" rows="3" />
            <flux:checkbox wire:model="activo" label="Subcategoría activa" />

            <div class="flex flex-col gap-3">
                <flux:button type="submit" variant="primary">
                    {{ $subcategoriaId ? 'Guardar Cambios' : 'Guardar Subcategoría' }}
                </flux:button>
                <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
