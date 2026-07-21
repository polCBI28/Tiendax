<div>
    <flux:modal wire:model="mostrarModal" class="max-w-3xl">
        <form wire:submit="guardar" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $categoriaId ? 'Editar Categoría' : 'Nueva Categoría' }}</flux:heading>
                <flux:subheading>Completa la información de la categoría.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <flux:input wire:model="nombre" label="Nombre" required />
                    <flux:textarea wire:model="descripcion" label="Descripción" rows="3" />

                    {{-- Selector de ícono --}}
                    <div class="space-y-3">
                        <flux:heading size="sm">Ícono de la categoría</flux:heading>
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border-2 border-blue-500/20 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[32px]">{{ $icono }}</span>
                            </div>
                            <flux:input wire:model.live.debounce.200ms="buscarIcono" icon="magnifying-glass" placeholder="Buscar ícono..." class="flex-1" />
                        </div>
                        <div class="max-h-56 overflow-y-auto space-y-3 pr-1 border border-zinc-200 dark:border-white/10 rounded-lg p-3">
                            @forelse($this->iconosFiltrados as $seccion => $lista)
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-zinc-400 mb-1.5">{{ $seccion }}</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($lista as $iconOption)
                                            <button type="button" wire:click="seleccionarIcono('{{ $iconOption }}')"
                                                    title="{{ $iconOption }}"
                                                    class="w-9 h-9 flex items-center justify-center rounded-lg border transition-all hover:border-blue-500 hover:bg-blue-500/10
                                                           {{ $icono === $iconOption ? 'border-blue-500 bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'border-zinc-200 dark:border-white/10 text-zinc-500' }}">
                                                <span class="material-symbols-outlined text-[18px]">{{ $iconOption }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <flux:text size="sm" class="text-zinc-400">Sin resultados para "{{ $buscarIcono }}".</flux:text>
                            @endforelse
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
                        <label for="categoria-imagen-input" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-lg border border-dashed border-zinc-300 dark:border-white/20 text-sm text-zinc-500 dark:text-zinc-400 cursor-pointer hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all">
                            <flux:icon.arrow-up-tray variant="micro" />
                            <span>{{ $imagen || $imagenActual ? 'Cambiar imagen' : 'Subir imagen' }}</span>
                        </label>
                        <input id="categoria-imagen-input" type="file" wire:model="imagen" accept="image/*" class="sr-only">
                        @error('imagen') <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text> @enderror
                    </div>

                    <flux:checkbox wire:model="activo" label="Categoría activa" />

                    <div class="flex flex-col gap-3">
                        <flux:button type="submit" variant="primary">
                            {{ $categoriaId ? 'Guardar Cambios' : 'Guardar Categoría' }}
                        </flux:button>
                        <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
                    </div>
                </div>

            </div>
        </form>
    </flux:modal>
</div>
