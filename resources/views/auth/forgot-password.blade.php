<x-guest-layout>
    <div class="mx-auto max-w-md">
        <p class="cs-eyebrow">Recuperar acceso</p>
        <h1 class="cs-display mt-3 text-4xl">Volvamos a entrar.</h1>
        <p class="mt-4 text-sm leading-6 text-muted">Indica tu correo y te enviaremos un enlace para crear una nueva contraseña.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="cs-eyebrow mb-2 block text-ink">Correo electrónico</label>
            <x-text-input id="email" class="cs-input cs-focus" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <button class="cs-button-signal cs-focus w-full">Enviar enlace <span aria-hidden="true">→</span></button>
        </div>
    </form>
    </div>
</x-guest-layout>
