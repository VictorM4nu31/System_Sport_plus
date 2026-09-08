<x-guest-layout>
    <div class="mx-auto max-w-md">
    <p class="cs-eyebrow">Un último paso</p>
    <h1 class="cs-display mt-3 text-4xl">Confirma tu correo.</h1>
    <p class="mt-4 text-sm leading-6 text-muted">Enviamos un enlace a tu correo para activar tu cuenta y mantenerla segura.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-6 rounded-md border border-success/30 bg-success/10 p-3 text-sm font-semibold text-success">
            Enviamos un nuevo enlace de verificación.
        </div>
    @endif

    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <button class="cs-button-signal cs-focus w-full sm:w-auto">Reenviar enlace</button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="cs-button-secondary cs-focus w-full sm:w-auto">
                Cerrar sesión
            </button>
        </form>
    </div>
    </div>
</x-guest-layout>
