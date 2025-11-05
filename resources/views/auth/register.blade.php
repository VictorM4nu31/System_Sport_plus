<x-auth-layout>
    <!-- Contenedor dividido en dos columnas con Flexbox -->
    <div class="flex flex-col lg:flex-row min-h-screen w-full">

        <!-- Columna izquierda: Espacio para la imagen -->
        <div class="hidden lg:block lg:w-1/2 bg-cover bg-center" style="background-image: url('img/registro.jpg');">
            <!-- Aquí va la imagen de fondo -->
        </div>

        <!-- Columna derecha: Formulario de registro -->
        <div class="flex flex-col justify-center w-full lg:w-1/2 bg-white p-8 lg:p-16">
            <!-- Título del formulario -->
            <h1 class="text-center text-display-sm text-primary mb-8">Registro</h1>

            <!-- Formulario de registro -->
            <form method="POST" action="{{ route('register') }}" class="w-full max-w-lg mx-auto">
                @csrf

                <!-- Nombre -->
                <div class="mb-6">
                    <label for="name" class="text-body-md text-primary-light">Nombre</label>
                    <input id="name" class="block mt-2 w-full border-primary-200 rounded-md p-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-body-md"
                           type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                </div>

                <!-- Correo electrónico -->
                <div class="mb-6">
                    <label for="email" class="text-body-md text-primary-light">Correo electrónico</label>
                    <input id="email" class="block mt-2 w-full border-primary-200 rounded-md p-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-body-md"
                           type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                </div>

                <!-- Contraseña -->
                <div class="mb-6">
                    <label for="password" class="text-body-md text-primary-light">Contraseña</label>
                    <input id="password" class="block mt-2 w-full border-primary-200 rounded-md p-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-body-md"
                           type="password" name="password" required autocomplete="new-password">
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mb-6">
                    <label for="password_confirmation" class="text-body-md text-primary-light">Confirmar Contraseña</label>
                    <input id="password_confirmation" class="block mt-2 w-full border-primary-200 rounded-md p-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-body-md"
                           type="password" name="password_confirmation" required autocomplete="new-password">
                </div>

                <!-- Botón de registro y enlace de inicio de sesión -->
                <div class="flex flex-col items-center justify-center mt-8 space-y-4">
                    <!-- Botón de registro -->
                    <button type="submit" class="w-full sm:w-auto bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary focus:outline-none transition-colors duration-200 text-body-md btn-accessible">
                        Registrarse
                    </button>

                    <!-- Texto debajo del botón, centrado -->
                    <a href="{{ route('login') }}" class="text-primary-light underline text-body-sm hover:text-primary transition-colors duration-200 link-accessible">
                        ¿Ya tienes una cuenta? Inicia sesión
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
