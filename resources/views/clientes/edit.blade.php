<x-layouts.app title="Editar Cliente">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 font-label-sm text-outline">
        <a href="{{ route('clientes.index') }}" class="hover:text-primary transition-colors">Clientes</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">{{ $cliente->nombre }}</span>
    </nav>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Editar Cliente</h2>
</div>

<div class="max-w-2xl">
    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 space-y-4">
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Nombre completo *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Documento (DNI / RUC)</label>
                <input type="text" name="documento" value="{{ old('documento', $cliente->documento) }}" maxlength="20"
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" maxlength="20"
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
            <div>
                <label class="font-label-lg text-on-surface-variant block mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $cliente->email) }}"
                       class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>

        @if($errors->any())
        <div class="mt-4 p-4 bg-error-container/20 border border-error/20 rounded-xl">
            <ul class="font-body-sm text-error space-y-1">
                @foreach($errors->all() as $error) <li>• {{ $error }}</li> @endforeach
            </ul>
        </div>
        @endif

        <div class="flex gap-3 mt-6">
            <button type="submit"
                    class="px-6 py-3 bg-secondary text-on-secondary rounded-xl font-label-lg shadow-sm hover:brightness-110 active:scale-95 transition-all">
                Actualizar Cliente
            </button>
            <a href="{{ route('clientes.index') }}"
               class="px-6 py-3 bg-surface-container-high text-on-surface rounded-xl font-label-lg hover:bg-outline-variant/20 transition-all">
                Cancelar
            </a>
        </div>
    </form>
</div>

</x-layouts.app>
