<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#131417">
    <title>@yield('codigo') — Campos Sport</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-paper font-sans antialiased">
    <main class="mx-auto flex min-h-screen w-[min(92vw,32rem)] flex-col items-center justify-center text-center">
        <p class="font-display text-display-lg text-carbon price-mono">@yield('codigo')</p>
        <h1 class="mt-2 text-heading-lg font-bold text-carbon">@yield('titulo')</h1>
        <p class="mt-2 text-body-md text-muted">@yield('mensaje')</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ url('/') }}" class="btn-carbon focus-volt no-underline">Ir a la tienda</a>
            <a href="{{ url('/productos') }}" class="btn-ghost focus-volt no-underline">Ver catálogo</a>
        </div>
    </main>
</body>
</html>
