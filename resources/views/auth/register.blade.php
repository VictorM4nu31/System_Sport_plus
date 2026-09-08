<x-guest-layout>
    <div class="mx-auto max-w-md">
        <p class="cs-eyebrow">Empieza a moverte</p>
        <h1 class="cs-display mt-3 text-4xl sm:text-5xl">Crea tu espacio.</h1>
        <p class="mt-4 text-sm leading-6 text-muted">Guarda favoritos, compara equipamiento y sigue cada pedido desde un solo lugar.</p>

        <x-validation-errors class="mt-6" />

        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="name" class="cs-eyebrow mb-2 block text-ink">Nombre</label>
                <input id="name" class="cs-input cs-focus" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <label for="email" class="cs-eyebrow mb-2 block text-ink">Correo electrónico</label>
                <input id="email" class="cs-input cs-focus" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <label for="password" class="cs-eyebrow mb-2 block text-ink">Contraseña</label>
                <input id="password" class="cs-input cs-focus" type="password" name="password" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <label for="password_confirmation" class="cs-eyebrow mb-2 block text-ink">Confirmar contraseña</label>
                <input id="password_confirmation" class="cs-input cs-focus" type="password" name="password_confirmation" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <button type="submit" class="cs-button-signal cs-focus w-full">Crear cuenta <span aria-hidden="true">→</span></button>
        </form>

        <p class="mt-8 text-center text-sm text-muted">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-bold text-ink underline underline-offset-4">Inicia sesión</a></p>
    </div>
</x-guest-layout>
