<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @php($title = 'Iniciar sesión — Sublimar Yamer')
    @include('partials.head')
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-zinc-950 antialiased flex items-center justify-center p-4 sm:p-8">
    {{ $slot }}
    @fluxScripts
</body>
</html>
