<x-layouts.app title="Subcategorías">

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
            <a href="{{ route('categorias.index') }}" class="hover:text-primary transition-colors">Catálogo</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Subcategorías</span>
        </nav>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Gestión de Subcategorías</h2>
        <p class="font-body-md text-on-surface-variant">Organiza tus categorías con subdivisiones específicas.</p>
    </div>
    <a href="{{ route('subcategorias.create') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-secondary text-on-secondary rounded-xl font-label-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
        <span class="material-symbols-outlined">add</span>
        Nueva Subcategoría
    </a>
</div>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container border-b border-outline-variant">
                <th class="px-6 py-4 font-label-lg text-on-surface">Nombre</th>
                <th class="px-6 py-4 font-label-lg text-on-surface">Categoría</th>
                <th class="px-6 py-4 font-label-lg text-on-surface">Descripción</th>
                <th class="px-6 py-4 font-label-lg text-on-surface">Estado</th>
                <th class="px-6 py-4 font-label-lg text-on-surface text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            @forelse($subcategorias as $sub)
            <tr class="table-row-hover transition-colors">
                <td class="px-6 py-4 font-label-lg text-on-surface">{{ $sub->nombre }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 bg-primary-container/10 text-primary font-label-sm rounded-full">
                        {{ $sub->categoria?->nombre ?? '—' }}
                    </span>
                </td>
                <td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ Str::limit($sub->descripcion, 50) ?: '—' }}</td>
                <td class="px-6 py-4">
                    @if($sub->activo)
                        <span class="px-2 py-1 bg-green-50 text-green-700 font-label-sm rounded-full">Activo</span>
                    @else
                        <span class="px-2 py-1 bg-surface-container-high text-on-surface-variant font-label-sm rounded-full">Inactivo</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('subcategorias.edit', $sub) }}"
                           class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        <form action="{{ route('subcategorias.destroy', $sub) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar esta subcategoría?')">
                            @csrf @method('DELETE')
                            <button class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-16 text-center text-on-surface-variant font-label-lg">
                    No hay subcategorías aún.
                    <a href="{{ route('subcategorias.create') }}" class="text-primary hover:underline ml-1">Agregar una</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

</x-layouts.app>
