<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div x-data="{ recovery: false }" class="mx-auto max-w-md">
            <p class="cs-eyebrow">Acceso protegido</p>
            <h1 class="cs-display mt-3 text-4xl">Un paso más.</h1>
            <div class="mt-4 text-sm leading-6 text-muted" x-show="! recovery">
                Introduce el código de tu aplicación autenticadora para confirmar tu identidad.
            </div>

            <div class="mt-4 text-sm leading-6 text-muted" x-cloak x-show="recovery">
                Introduce uno de tus códigos de recuperación de emergencia.
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('two-factor.login') }}" class="mt-8 space-y-5">
                @csrf

                <div class="mt-4" x-show="! recovery">
                    <label for="code" class="cs-eyebrow mb-2 block text-ink">Código de autenticación</label>
                    <x-input id="code" class="cs-input cs-focus" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                </div>

                <div class="mt-4" x-cloak x-show="recovery">
                    <label for="recovery_code" class="cs-eyebrow mb-2 block text-ink">Código de recuperación</label>
                    <x-input id="recovery_code" class="cs-input cs-focus" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <button type="button" class="text-left text-sm font-semibold text-muted underline underline-offset-4 hover:text-ink"
                                    x-show="! recovery"
                                    x-on:click="
                                        recovery = true;
                                        $nextTick(() => { $refs.recovery_code.focus() })
                                    ">
                        Usar código de recuperación
                    </button>

                    <button type="button" class="text-left text-sm font-semibold text-muted underline underline-offset-4 hover:text-ink"
                                    x-cloak
                                    x-show="recovery"
                                    x-on:click="
                                        recovery = false;
                                        $nextTick(() => { $refs.code.focus() })
                                    ">
                        Usar código de autenticación
                    </button>

                    <button class="cs-button-signal cs-focus">Entrar <span aria-hidden="true">→</span></button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
