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
    <div class="cs-noise min-h-screen px-4 py-6 sm:px-6">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-3xl items-center justify-center">
            <main class="w-full overflow-hidden rounded-[20px] border border-line bg-white text-center shadow-[0_18px_70px_rgba(17,19,21,.12)]">
                <div class="flex items-center justify-center gap-2 bg-ink px-6 py-4">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-volt text-xs font-black text-ink" aria-hidden="true">CS</span>
                    <span class="font-display text-lg font-extrabold tracking-tight text-white">Campos Sport</span>
                </div>
                <div class="px-6 py-10 sm:px-12">
                    <p class="cs-eyebrow">Algo no salió como esperabas</p>
                    <p class="font-display mt-3 text-6xl font-black tracking-tight text-ink sm:text-7xl" role="text" aria-label="Error @yield('codigo')">@yield('codigo')</p>
                    <h1 class="cs-display mt-3 text-3xl sm:text-4xl">@yield('titulo')</h1>
                    <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-muted">@yield('mensaje')</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <button type="button" onclick="history.back()" class="btn-ghost focus-volt min-h-[44px]">Volver atrás</button>
                        <a href="{{ url('/') }}" class="btn-carbon focus-volt inline-flex min-h-[44px] items-center no-underline">Ir a la tienda</a>
                        <a href="{{ url('/productos') }}" class="cs-button-signal focus-volt inline-flex min-h-[44px] items-center no-underline">Ver catálogo</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
