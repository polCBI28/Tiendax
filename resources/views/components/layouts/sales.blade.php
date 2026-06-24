<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Sublimar Yamer — Registro de Venta</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Hanken+Grotesk:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "secondary": "#b80049",
                        "surface": "#f8f9fa",
                        "on-primary-fixed-variant": "#293ca0",
                        "tertiary-fixed-dim": "#44d8f1",
                        "inverse-surface": "#2e3132",
                        "on-surface-variant": "#454652",
                        "error-container": "#ffdad6",
                        "inverse-primary": "#bac3ff",
                        "outline": "#757684",
                        "primary-fixed-dim": "#bac3ff",
                        "tertiary-fixed": "#a1efff",
                        "on-secondary-container": "#fffbff",
                        "secondary-fixed-dim": "#ffb2be",
                        "surface-tint": "#4355b9",
                        "on-primary-container": "#cacfff",
                        "background": "#f8f9fa",
                        "on-tertiary-container": "#55e4fd",
                        "on-tertiary": "#ffffff",
                        "surface-container-low": "#f3f4f5",
                        "primary": "#24389c",
                        "tertiary-container": "#006471",
                        "on-background": "#191c1d",
                        "on-error-container": "#93000a",
                        "on-surface": "#191c1d",
                        "on-primary": "#ffffff",
                        "surface-container": "#edeeef",
                        "primary-container": "#3f51b5",
                        "outline-variant": "#c5c5d4",
                        "on-secondary-fixed": "#400014",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-fixed-variant": "#004e59",
                        "on-secondary": "#ffffff",
                        "surface-container-highest": "#e1e3e4",
                        "on-secondary-fixed-variant": "#900038",
                        "on-tertiary-fixed": "#001f25",
                        "on-error": "#ffffff",
                        "primary-fixed": "#dee0ff",
                        "on-primary-fixed": "#00105c",
                        "secondary-fixed": "#ffd9de",
                        "surface-variant": "#e1e3e4",
                        "surface-container-high": "#e7e8e9",
                        "surface-dim": "#d9dadb",
                        "tertiary": "#004a55",
                        "inverse-on-surface": "#f0f1f2",
                        "secondary-container": "#e2165f",
                        "error": "#ba1a1a",
                        "surface-bright": "#f8f9fa"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "sidebar-width": "260px",
                        "stack-lg": "2rem",
                        "stack-md": "1rem",
                        "stack-sm": "0.5rem",
                        "gutter": "1.5rem",
                        "container-padding": "2rem"
                    },
                    "fontFamily": {
                        "label-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-xl": ["Hanken Grotesk"],
                        "body-lg": ["Inter"],
                        "label-lg": ["Inter"],
                        "headline-md": ["Hanken Grotesk"],
                        "headline-lg": ["Hanken Grotesk"],
                        "body-sm": ["Inter"],
                        "mono-data": ["Inter"]
                    },
                    "fontSize": {
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-xl": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "label-lg": ["14px", {"lineHeight": "20px", "letterSpacing": "0.02em", "fontWeight": "600"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "mono-data": ["14px", {"lineHeight": "20px", "fontWeight": "500"}]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes pulse-subtle {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .active-row { animation: pulse-subtle 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen overflow-hidden">

{{-- SIDEBAR --}}
<aside class="fixed h-full w-[260px] left-0 top-0 bg-surface-container-highest shadow-sm flex flex-col py-gutter z-50">
    <div class="px-gutter mb-8">
        <h1 class="font-headline-md text-headline-md font-bold text-primary leading-tight">Sublimar Yamer</h1>
        <p class="font-label-sm text-label-sm text-on-surface-variant">Gestión Minorista</p>
    </div>
    <nav class="flex-1 px-4 space-y-1">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-lg text-label-lg">Panel de Control</span>
        </a>
        <a href="{{ route('categorias.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200">
            <span class="material-symbols-outlined">category</span>
            <span class="font-label-lg text-label-lg">Catálogo</span>
        </a>
        <a href="{{ route('productos.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200">
            <span class="material-symbols-outlined">inventory_2</span>
            <span class="font-label-lg text-label-lg">Inventario</span>
        </a>
        <a href="{{ route('ventas.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg border-l-4 border-primary bg-primary-container/10 text-primary font-bold">
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
    </div>
</aside>

{{-- HEADER VENTAS --}}
<header class="fixed top-0 right-0 w-[calc(100%-260px)] flex justify-between items-center h-16 px-gutter bg-surface shadow-sm z-40">
    <div class="flex items-center gap-4">
        <span class="font-headline-md text-headline-md font-bold text-primary">Registro de Venta Manual</span>
        <div class="bg-primary-container/20 text-primary px-3 py-1 rounded-full font-label-sm flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">timer</span>
            Sesión: {{ now()->format('h:i A') }}
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button class="p-2 rounded-full hover:bg-surface-container-low text-on-surface-variant relative transition-all">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-secondary rounded-full"></span>
        </button>
        <div class="flex items-center gap-3 cursor-pointer hover:bg-surface-container-low px-3 py-1.5 rounded-full transition-all">
            <div>
                <p class="font-label-lg text-label-lg text-on-surface leading-none">{{ auth()->user()->name }}</p>
                <p class="font-label-sm text-label-sm text-outline">Administrador</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>
</header>

{{-- CONTENIDO FULL HEIGHT --}}
<div class="ml-[260px] pt-16 h-screen overflow-hidden">
    {{ $slot }}
</div>

{{-- Keyboard hints --}}
<div class="fixed bottom-4 left-[280px] flex space-x-3 z-50">
    <div class="bg-inverse-surface text-inverse-on-surface px-3 py-1.5 rounded-lg font-label-sm shadow-md flex items-center gap-2">
        <kbd class="bg-outline-variant/50 text-on-surface px-1.5 py-0.5 rounded text-[10px] font-mono">ESC</kbd>
        Cancelar
    </div>
    <div class="bg-inverse-surface text-inverse-on-surface px-3 py-1.5 rounded-lg font-label-sm shadow-md flex items-center gap-2">
        <kbd class="bg-outline-variant/50 text-on-surface px-1.5 py-0.5 rounded text-[10px] font-mono">F10</kbd>
        Registrar
    </div>
    <div class="bg-inverse-surface text-inverse-on-surface px-3 py-1.5 rounded-lg font-label-sm shadow-md flex items-center gap-2">
        <kbd class="bg-outline-variant/50 text-on-surface px-1.5 py-0.5 rounded text-[10px] font-mono">F2</kbd>
        Buscar
    </div>
</div>
</body>
</html>
