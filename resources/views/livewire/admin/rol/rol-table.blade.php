<div>

    @if($mensaje)
        <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
    @endif
    @error('protegido')
        <flux:callout icon="exclamation-triangle" variant="danger" heading="{{ $message }}" class="mb-6" />
    @enderror

    <flux:card class="overflow-hidden p-0">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 p-4 border-b border-zinc-200 dark:border-white/10">
            <flux:input
                wire:model.live.debounce.400ms="search"
                icon="magnifying-glass"
                placeholder="Buscar rol..."
                class="flex-1 min-w-[200px]"
            />

            <div class="flex items-center gap-2 ml-auto">
                <span class="text-sm text-zinc-400 whitespace-nowrap">
                    {{ $roles->total() }} rol{{ $roles->total() === 1 ? '' : 'es' }}
                </span>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="px-4" wire:loading.class="opacity-60">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                        <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 min-w-[200px]">Rol</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Usuarios</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-32">Permisos</th>
                        <th class="text-center px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 w-28">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $rol)
                        <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center shrink-0">
                                        <flux:icon.shield-check variant="mini" class="text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <p class="font-medium text-zinc-800 dark:text-white">{{ $rol->name }}</p>
                                    @if($rol->name === 'Super Admin')
                                        <flux:badge size="sm" color="amber">Protegido</flux:badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">{{ $rol->users_count }}</td>
                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">{{ $rol->permissions_count }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                    <button wire:click="editar({{ $rol->id }})"
                                            class="p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                                            title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @if($rol->name !== 'Super Admin')
                                        <button wire:click="eliminar({{ $rol->id }})"
                                                wire:confirm="¿Eliminar este rol? Los usuarios que lo tengan se quedarán sin rol asignado."
                                                class="p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-zinc-400 hover:text-red-600 dark:hover:text-red-400"
                                                title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-zinc-400">
                                <div class="flex flex-col items-center gap-3">
                                    <flux:icon.shield-check class="size-12" />
                                    <p>No se encontraron roles con los filtros aplicados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-white/10">
                {{ $roles->links() }}
            </div>
        @endif
    </flux:card>

</div>
