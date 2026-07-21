<div>

    <div class="mb-6">
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item href="{{ route('clientes.index') }}" wire:navigate>Clientes</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $cliente->nombre }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ $cliente->nombre }}</flux:heading>
            <flux:button href="{{ route('clientes.index', ['editar' => $cliente->id]) }}" wire:navigate icon="pencil">
                Editar
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="space-y-4">
            <flux:card class="space-y-4">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center shrink-0">
                        <span class="text-blue-600 dark:text-blue-400 font-semibold text-xl uppercase">{{ substr($cliente->nombre, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-zinc-800 dark:text-white">{{ $cliente->nombre }}</p>
                        <flux:text size="sm" class="text-zinc-400">Cliente registrado</flux:text>
                    </div>
                </div>
                @if($cliente->documento)
                    <div class="flex gap-3 items-start">
                        <flux:icon.identification class="size-5 text-zinc-400 mt-0.5" />
                        <div>
                            <flux:text size="sm" class="text-zinc-400">Documento</flux:text>
                            <p class="font-mono text-zinc-800 dark:text-white">{{ $cliente->documento }}</p>
                        </div>
                    </div>
                @endif
                @if($cliente->telefono)
                    <div class="flex gap-3 items-start">
                        <flux:icon.phone class="size-5 text-zinc-400 mt-0.5" />
                        <div>
                            <flux:text size="sm" class="text-zinc-400">Teléfono</flux:text>
                            <p class="text-zinc-800 dark:text-white">{{ $cliente->telefono }}</p>
                        </div>
                    </div>
                @endif
                @if($cliente->email)
                    <div class="flex gap-3 items-start">
                        <flux:icon.envelope class="size-5 text-zinc-400 mt-0.5" />
                        <div>
                            <flux:text size="sm" class="text-zinc-400">Correo</flux:text>
                            <p class="text-zinc-800 dark:text-white">{{ $cliente->email }}</p>
                        </div>
                    </div>
                @endif
            </flux:card>

            <flux:card>
                <flux:subheading>Total de compras</flux:subheading>
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400 mt-1">{{ $cliente->ventas->count() }}</flux:heading>
            </flux:card>
        </div>

        <flux:card class="lg:col-span-2 overflow-hidden p-0">
            <div class="p-6 border-b border-zinc-200 dark:border-white/10">
                <flux:heading size="lg">Historial de Ventas</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5">
                            <th class="text-left px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">N° Boleta</th>
                            <th class="text-left px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Fecha</th>
                            <th class="text-right px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Total</th>
                            <th class="text-center px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cliente->ventas as $venta)
                            @php
                                $estadoColor = match($venta->estado) {
                                    'completado' => 'green',
                                    'pendiente' => 'amber',
                                    'cancelado' => 'red',
                                    default => 'zinc',
                                };
                            @endphp
                            <tr class="border-b border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-mono text-zinc-800 dark:text-white">{{ $venta->numero_boleta }}</td>
                                <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-zinc-800 dark:text-white">S/ {{ number_format($venta->total, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <flux:badge size="sm" :color="$estadoColor">{{ ucfirst($venta->estado) }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <flux:button href="{{ route('ventas.show', $venta) }}" wire:navigate icon="arrow-top-right-on-square" variant="ghost" size="sm" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-zinc-400">
                                    Este cliente no tiene ventas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>

    </div>

</div>
