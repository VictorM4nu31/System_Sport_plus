<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#FFFFFF] leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Contenedor para el formulario de actualización de perfil -->
            <div class="p-8 bg-[#FFFFFF] bg-opacity-50 shadow-xl rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Contenedor para el formulario de cambio de contraseña -->
            <div class="p-8 bg-[#FFFFFF] bg-opacity-50 shadow-xl rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Contenedor para el formulario de eliminación de cuenta -->
            <div class="p-8 bg-[#FFFFFF] bg-opacity-50 shadow-xl rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
