<div>
    <flux:modal wire:model="mostrarModal" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Importar Categorías</flux:heading>
                <flux:subheading>Sube un archivo Excel o CSV para crear o actualizar categorías por nombre.</flux:subheading>
            </div>

            <flux:button wire:click="descargarPlantilla" variant="ghost" size="sm" icon="arrow-down-tray">
                Descargar plantilla
            </flux:button>

            <div>
                <flux:input type="file" wire:model="archivo" label="Archivo (.xlsx, .csv)" accept=".xlsx,.csv,.xls" />
                @error('archivo') <flux:text size="sm" class="text-red-600 dark:text-red-400 mt-1">{{ $message }}</flux:text> @enderror
            </div>

            @if($creados !== null)
                <flux:callout icon="check-circle" variant="success"
                    heading="Importación completada: {{ $creados }} creados, {{ $actualizados }} actualizados." />
            @endif

            @if(count($errores) > 0)
                <div class="max-h-40 overflow-y-auto space-y-1 border border-red-200 dark:border-red-900/30 rounded-lg p-3 bg-red-50 dark:bg-red-900/10">
                    @foreach($errores as $error)
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex gap-3">
                <flux:button wire:click="importar" variant="primary" wire:loading.attr="disabled" class="flex-1 justify-center">
                    Importar
                </flux:button>
                <flux:button wire:click="cerrar" class="flex-1 justify-center">Cerrar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
