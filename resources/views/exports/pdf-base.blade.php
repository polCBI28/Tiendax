<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #27272a; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #71717a; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e4e4e7; text-align: left; }
        th { background: #f4f4f5; font-weight: bold; text-transform: uppercase; font-size: 9px; color: #52525b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totales { margin-top: 16px; font-size: 11px; }
        .totales strong { color: #18181b; }
    </style>
</head>
<body>
    <h1>@yield('titulo')</h1>
    <p class="meta">Generado el {{ now()->format('d/m/Y H:i') }} @yield('subtitulo')</p>
    @yield('content')
</body>
</html>
