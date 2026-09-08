<x-guest-layout>
    <div class="mx-auto max-w-md">
        <p class="cs-eyebrow">Bienvenido de nuevo</p>
        <h1 class="cs-display mt-3 text-4xl sm:text-5xl">Entra y sigue tu ritmo.</h1>
        <p class="mt-4 text-sm leading-6 text-muted">Accede a tu catálogo, pedidos y equipamiento guardado.</p>

        <x-validation-errors class="mt-6" />

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="cs-eyebrow mb-2 block text-ink">Correo electrónico</label>
                <input id="email" class="cs-input cs-focus" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <div class="flex items-center justify-between gap-3">
                    <label for="password" class="cs-eyebrow block text-ink">Contraseña</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-muted underline underline-offset-4 hover:text-ink">¿La olvidaste?</a>
                    @endif
                </div>
                <input id="password" class="cs-input cs-focus mt-2" type="password" name="password" required autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-muted">
                <input id="remember_me" type="checkbox" class="rounded border-line text-ink focus:ring-volt" name="remember">
                Recordarme en este dispositivo
            </label>
            <button type="submit" class="cs-button-signal cs-focus w-full">Iniciar sesión <span aria-hidden="true">→</span></button>
        </form>

        <p class="mt-8 text-center text-sm text-muted">¿Aún no tienes cuenta? <a href="{{ route('register') }}" class="font-bold text-ink underline underline-offset-4">Crea tu acceso</a></p>
    </div>
</x-guest-layout>
