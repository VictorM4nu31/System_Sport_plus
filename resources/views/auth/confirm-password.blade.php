<x-guest-layout>
    <div class="mx-auto max-w-md">
    <p class="cs-eyebrow">Zona segura</p>
    <h1 class="cs-display mt-3 text-4xl">Confirma que eres tú.</h1>
    <p class="mt-4 text-sm leading-6 text-muted">Necesitamos tu contraseña para continuar en esta sección protegida.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="cs-eyebrow mb-2 block text-ink">Contraseña</label>
            <x-text-input id="password" class="cs-input cs-focus"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end">
            <button class="cs-button-signal cs-focus w-full">Confirmar acceso <span aria-hidden="true">→</span></button>
        </div>
    </form>
    </div>
</x-guest-layout>
