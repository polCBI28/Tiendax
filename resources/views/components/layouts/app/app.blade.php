<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Sublimar Yamer - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Hanken+Grotesk:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "secondary": "#b80049",
                        "surface": "#f8f9fa",
                        "on-primary-fixed-variant": "#293ca0",
                        "inverse-surface": "#2e3132",
                        "on-surface-variant": "#454652",
                        "outline": "#757684",
                        "background": "#f8f9fa",
                        "on-background": "#191c1d",
                        "on-surface": "#191c1d",
                        "on-primary": "#ffffff",
                        "surface-container": "#edeeef",
                        "primary-container": "#3f51b5",
                        "outline-variant": "#c5c5d4",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary": "#ffffff",
                        "surface-container-highest": "#e1e3e4",
                        "on-error": "#ffffff",
                        "primary-fixed": "#dee0ff",
                        "on-primary-fixed": "#00105c",
                        "surface-variant": "#e1e3e4",
                        "surface-container-high": "#e7e8e9",
                        "surface-container-low": "#f3f4f5",
                        "primary": "#24389c",
                        "on-tertiary-container": "#55e4fd",
                        "on-tertiary": "#ffffff",
                        "tertiary-container": "#006471",
                        "surface-dim": "#d9dadb",
                        "tertiary": "#004a55",
                        "secondary-container": "#e2165f",
                        "error": "#ba1a1a",
                        "on-secondary-fixed-variant": "#900038",
                        "secondary-fixed": "#ffd9de",
                        "tertiary-fixed": "#a1efff",
                        "tertiary-fixed-dim": "#44d8f1",
                        "inverse-primary": "#bac3ff",
                        "primary-fixed-dim": "#bac3ff",
                        "on-primary-container": "#cacfff",
                        "on-secondary-container": "#fffbff",
                        "on-secondary-fixed": "#400014",
                        "surface-tint": "#4355b9",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
                        "inverse-on-surface": "#f0f1f2",
                        "surface-bright": "#f8f9fa",
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "sidebar-width": "260px",
                        "gutter": "1.5rem",
                        "container-padding": "2rem"
                    },
                    fontFamily: {
                        "label-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-xl": ["Hanken Grotesk"],
                        "body-lg": ["Inter"],
                        "label-lg": ["Inter"],
                        "headline-md": ["Hanken Grotesk"],
                        "headline-lg": ["Hanken Grotesk"],
                        "body-sm": ["Inter"],
                    },
                    fontSize: {
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-xl": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "label-lg": ["14px", {"lineHeight": "20px", "letterSpacing": "0.02em", "fontWeight": "600"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .category-card:hover .category-image { transform: scale(1.05); }
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-surface text-on-surface font-body-md overflow-x-hidden">

{{-- SIDEBAR --}}
<aside class="fixed h-full w-[260px] left-0 top-0 bg-surface-container-highest shadow-sm flex flex-col py-gutter z-50">
    <div class="px-gutter mb-8">
        <h1 class="font-headline-md text-headline-md font-bold text-primary leading-tight">Sublimar Yamer</h1>
        <p class="font-label-sm text-label-sm text-on-surface-variant">Gestión Minorista</p>
    </div>
    <nav class="flex-1 px-4 space-y-1">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'border-l-4 border-primary bg-primary-container/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors duration-200">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-lg text-label-lg">Panel de Control</span>
        </a>
        <a href="{{ route('categorias.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('categorias.*') ? 'border-l-4 border-primary bg-primary-container/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors duration-200">
            <span class="material-symbols-outlined">category</span>
            <span class="font-label-lg text-label-lg">Catálogo</span>
        </a>
        <a href="{{ route('productos.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('productos.*') ? 'border-l-4 border-primary bg-primary-container/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors duration-200">
            <span class="material-symbols-outlined">inventory_2</span>
            <span class="font-label-lg text-label-lg">Inventario</span>
        </a>
        <a href="{{ route('ventas.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('ventas.*') ? 'border-l-4 border-primary bg-primary-container/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors duration-200">
            <span class="material-symbols-outlined">point_of_sale</span>
            <span class="font-label-lg text-label-lg">Ventas</span>
        </a>
        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200">
            <span class="material-symbols-outlined">analytics</span>
            <span class="font-label-lg text-label-lg">Reportes</span>
        </a>
    </nav>
    <div class="mt-auto px-4 pt-4 border-t border-outline-variant/30">
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-label-lg text-label-lg">Configuración</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200">
            <span class="material-symbols-outlined">help</span>
            <span class="font-label-lg text-label-lg">Soporte</span>
        </a>
        <div class="mt-6 p-4 rounded-xl bg-primary-container text-on-primary-container">
            <a href="{{ route('ventas.create') }}" class="block w-full py-2 bg-primary text-on-primary rounded-lg font-bold shadow-md hover:brightness-110 active:scale-95 transition-all text-center">
                + Registrar Venta
            </a>
        </div>
    </div>
</aside>

{{-- HEADER --}}
<header class="fixed top-0 right-0 w-[calc(100%-260px)] flex justify-between items-center h-16 px-gutter bg-surface shadow-sm z-40">
    <div class="flex items-center gap-4 bg-surface-container-low px-4 py-2 rounded-full w-96">
        <span class="material-symbols-outlined text-outline">search</span>
        <input class="bg-transparent border-none focus:ring-0 text-body-sm w-full" placeholder="Buscar pedidos, productos..." type="text">
    </div>
    <div class="flex items-center gap-4">
        <button class="p-2 rounded-full hover:bg-surface-container-low text-on-surface-variant relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-secondary rounded-full"></span>
        </button>
        <div class="h-8 w-[1px] bg-outline-variant"></div>
        <div class="flex items-center gap-3 pl-2">
            <div class="text-right hidden sm:block">
                <p class="font-label-lg text-label-lg leading-none">{{ auth()->user()->name }}</p>
                <p class="font-label-sm text-label-sm text-outline">Administrador</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>
</header>

{{-- CONTENIDO --}}
<main class="ml-[260px] pt-24 pb-12 px-container-padding min-h-screen">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl font-label-lg">
            ✓ {{ session('success') }}
        </div>
    @endif
    @yield('content')
</main>

@stack('scripts')
<script>
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('mousedown', () => card.style.transform = 'scale(0.98)');
        card.addEventListener('mouseup', () => card.style.transform = 'scale(1)');
        card.addEventListener('mouseleave', () => card.style.transform = 'scale(1)');
        card.addEventListener('mousemove', (e) => {
            const img = card.querySelector('.category-image');
            if (!img) return;
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width;
            const y = (e.clientY - rect.top) / rect.height;
            img.style.transformOrigin = `${x * 100}% ${y * 100}%`;
        });
    });
</script>
</body>
</html>