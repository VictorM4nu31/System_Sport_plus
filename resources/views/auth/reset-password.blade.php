<x-guest-layout>
    <div class="mx-auto max-w-md">
    <p class="cs-eyebrow">Nueva contraseña</p>
    <h1 class="cs-display mt-3 text-4xl">Hazla fácil de recordar.</h1>
    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="cs-eyebrow mb-2 block text-ink">Correo electrónico</label>
            <x-text-input id="email" class="cs-input cs-focus" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="cs-eyebrow mb-2 block text-ink">Nueva contraseña</label>
            <x-text-input id="password" class="cs-input cs-focus" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="cs-eyebrow mb-2 block text-ink">Confirmar contraseña</label>

            <x-text-input id="password_confirmation" class="cs-input cs-focus"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <button class="cs-button-signal cs-focus w-full">Actualizar contraseña <span aria-hidden="true">→</span></button>
        </div>
    </form>
    </div>
</x-guest-layout>
