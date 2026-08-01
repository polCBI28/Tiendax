<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 flex items-center justify-center p-6">
        @php
            $colores = [
                'zinc' => ['bg' => 'bg-zinc-100 dark:bg-white/5', 'text' => 'text-zinc-500 dark:text-zinc-400'],
                'amber' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400'],
                'red' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-600 dark:text-red-400'],
            ];
            $paleta = $colores[trim($__env->yieldContent('color', 'zinc'))] ?? $colores['zinc'];
        @endphp
        <div class="w-full max-w-md text-center">
            <div class="flex items-center justify-center gap-2 mb-8">
                <span class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground font-bold text-sm">SY</span>
                <span class="font-semibold text-sm">Sublimar Yamer</span>
            </div>

            <div class="w-20 h-20 mx-auto rounded-2xl {{ $paleta['bg'] }} flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-[40px] {{ $paleta['text'] }}">@yield('icon', 'error')</span>
            </div>

            <flux:heading size="xl" class="mb-2">@yield('codigo') @yield('titulo', 'Ha ocurrido un error')</flux:heading>
            <flux:subheading class="mb-8">@yield('descripcion')</flux:subheading>

            <div class="flex flex-col items-center gap-3">
                @yield('acciones')

                @unless(trim($__env->yieldContent('sinAcciones')) === '1')
                    @auth
                        <flux:button variant="primary" href="{{ route('dashboard') }}" wire:navigate class="w-full justify-center">
                            Ir al Panel de Control
                        </flux:button>
                    @else
                        <flux:button variant="primary" href="{{ route('login') }}" wire:navigate class="w-full justify-center">
                            Iniciar sesión
                        </flux:button>
                    @endauth
                @endunless
            </div>
        </div>

        @fluxScripts
    </body>
</html>
