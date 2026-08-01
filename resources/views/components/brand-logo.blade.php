@props(['size' => 'sm'])

@php
    $logoPath = \App\Models\Configuracion::actual()->logo_path;
@endphp

@if($logoPath)
    <img
        src="{{ asset('storage/'.$logoPath) }}"
        alt="Logo"
        {{ $attributes->class([
            'object-contain shrink-0',
            'h-8 w-auto max-w-40' => $size === 'sm',
            'h-14 w-auto max-w-56' => $size === 'lg',
        ]) }}
    >
@elseif($size === 'lg')
    <div {{ $attributes->class('flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-600/20 border border-indigo-200 dark:border-indigo-500/30 shrink-0') }}>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-7 h-7 text-indigo-600 dark:text-indigo-300">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.35m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
        </svg>
    </div>
@else
    <span {{ $attributes->class('flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground font-bold text-sm shrink-0') }}>SY</span>
@endif
