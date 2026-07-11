<x-layouts.app title="Detalle de Cliente">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('clientes.index') }}" class="hover:text-primary transition-colors">Clientes</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $cliente->nombre }}</span>
    </nav>
    <div class="flex items-center justify-between">
        <h2 class="font-headline-lg text-headline-lg text-on-surface">{{ $cliente->nombre }}</h2>
        <a href="{{ route('clientes.index', ['editar' => $cliente->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-label-lg hover:bg-outline-variant/20 transition-all">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Editar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="space-y-4">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-14 h-14 rounded-full bg-secondary-container/30 flex items-center justify-center">
                    <span class="font-headline-md text-secondary uppercase">{{ substr($cliente->nombre, 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-label-lg text-on-surface">{{ $cliente->nombre }}</p>
                    <p class="font-body-sm text-on-surface-variant">Cliente registrado</p>
                </div>
            </div>
            @if($cliente->documento)
            <div class="flex gap-3 items-start">
                <span class="material-symbols-outlined text-on-surface-variant mt-0.5 text-[20px]">badge</span>
                <div>
                    <p class="font-label-sm text-on-surface-variant">Documento</p>
                    <p class="font-mono-data text-on-surface">{{ $cliente->documento }}</p>
                </div>
            </div>
            @endif
            @if($cliente->telefono)
            <div class="flex gap-3 items-start">
                <span class="material-symbols-outlined text-on-surface-variant mt-0.5 text-[20px]">phone</span>
                <div>
                    <p class="font-label-sm text-on-surface-variant">Teléfono</p>
                    <p class="font-body-sm text-on-surface">{{ $cliente->telefono }}</p>
                </div>
            </div>
            @endif
            @if($cliente->email)
            <div class="flex gap-3 items-start">
                <span class="material-symbols-outlined text-on-surface-variant mt-0.5 text-[20px]">email</span>
                <div>
                    <p class="font-label-sm text-on-surface-variant">Correo</p>
                    <p class="font-body-sm text-on-surface">{{ $cliente->email }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6">
            <p class="font-label-sm text-on-surface-variant mb-1">Total de compras</p>
            <p class="font-headline-md text-primary">{{ $cliente->ventas->count() }}</p>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-6 border-b border-outline-variant flex items-center justify-between">
                <h3 class="font-headline-md text-headline-md text-on-surface">Historial de Ventas</h3>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-container border-b border-outline-variant">
                        <th class="px-6 py-3 font-label-lg text-on-surface text-left">N° Boleta</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-left">Fecha</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-right">Total</th>
                        <th class="px-6 py-3 font-label-lg text-on-surface text-center">Estado</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($cliente->ventas as $venta)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 font-mono-data text-on-surface">{{ $venta->numero_boleta }}</td>
                        <td class="px-6 py-4 font-body-sm text-on-surface-variant">
                            {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 font-mono-data text-on-surface text-right">
                            S/ {{ number_format($venta->total, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($venta->estado === 'completado')
                                <span class="px-2 py-0.5 bg-green-50 text-green-700 font-label-sm rounded-full">Completado</span>
                            @elseif($venta->estado === 'pendiente')
                                <span class="px-2 py-0.5 bg-primary-container/20 text-primary font-label-sm rounded-full">Pendiente</span>
                            @else
                                <span class="px-2 py-0.5 bg-surface-container-high text-on-surface-variant font-label-sm rounded-full">{{ ucfirst($venta->estado) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('ventas.show', $venta) }}"
                               class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-all inline-flex">
                                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-on-surface-variant font-label-lg">
                            Este cliente no tiene ventas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</x-layouts.app>
