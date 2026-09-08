<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Campos Sport · Tu próxima sesión</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-paper text-ink">
        <div class="min-h-screen cs-noise px-4 py-6 sm:px-6 lg:px-10">
            <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-6xl items-center justify-center">
                <div class="grid w-full overflow-hidden rounded-[20px] border border-line bg-white shadow-[0_18px_70px_rgba(17,19,21,.12)] lg:grid-cols-[.85fr_1.15fr]">
                    <aside class="hidden flex-col justify-between bg-ink p-10 text-white lg:flex">
                        <div>
                            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-3 no-underline text-white">
                                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-volt font-black text-ink">CS</span>
                                <span class="font-display text-lg font-extrabold tracking-tight">Campos Sport</span>
                            </a>
                            <p class="cs-eyebrow mt-20 text-volt">Tu próxima sesión empieza aquí</p>
                            <h1 class="cs-display mt-4 max-w-sm text-5xl text-white">Equipo que sigue tu ritmo.</h1>
                            <p class="mt-6 max-w-sm text-sm leading-6 text-white/65">Descubre equipamiento pensado para moverte mejor, entrenar con intención y llegar preparado.</p>
                        </div>
                        <p class="text-xs text-white/45">Campos Sport · Equipamiento deportivo</p>
                    </aside>
                    <main class="min-w-0 p-6 sm:p-10 lg:p-14">
                        <div class="mb-8 flex items-center justify-between lg:hidden">
                            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 font-display font-extrabold text-ink no-underline"><span class="flex h-9 w-9 items-center justify-center rounded-md bg-volt text-xs font-black">CS</span> Campos Sport</a>
                            <span class="cs-eyebrow">Tu próxima sesión</span>
                        </div>
                {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
