<x-layouts.app title="Clientes">

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Gestión de Clientes</h2>
        <p class="font-body-md text-on-surface-variant">Administra el directorio de clientes registrados.</p>
    </div>
    <a href="{{ route('clientes.create') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-secondary text-on-secondary rounded-xl font-label-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
        <span class="material-symbols-outlined">person_add</span>
        Nuevo Cliente
    </a>
</div>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container border-b border-outline-variant">
                <th class="px-6 py-4 font-label-lg text-on-surface">Cliente</th>
                <th class="px-6 py-4 font-label-lg text-on-surface">Documento</th>
                <th class="px-6 py-4 font-label-lg text-on-surface">Teléfono</th>
                <th class="px-6 py-4 font-label-lg text-on-surface">Email</th>
                <th class="px-6 py-4 font-label-lg text-on-surface text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            @forelse($clientes as $cliente)
            <tr class="table-row-hover transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary-container/20 flex items-center justify-center flex-shrink-0">
                            <span class="font-label-lg text-primary uppercase">{{ substr($cliente->nombre, 0, 1) }}</span>
                        </div>
                        <span class="font-label-lg text-on-surface">{{ $cliente->nombre }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 font-mono-data text-on-surface-variant">{{ $cliente->documento ?? '—' }}</td>
                <td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $cliente->telefono ?? '—' }}</td>
                <td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $cliente->email ?? '—' }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('clientes.show', $cliente) }}"
                           class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all" title="Ver detalle">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </a>
                        <a href="{{ route('clientes.edit', $cliente) }}"
                           class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all" title="Editar">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar este cliente?')">
                            @csrf @method('DELETE')
                            <button class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-lg transition-all" title="Eliminar">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-16 text-center text-on-surface-variant font-label-lg">
                    No hay clientes registrados.
                    <a href="{{ route('clientes.create') }}" class="text-primary hover:underline ml-1">Agregar uno</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($clientes->hasPages())
    <div class="p-4 bg-surface border-t border-outline-variant">
        {{ $clientes->links() }}
    </div>
    @endif
</div>

</x-layouts.app>
