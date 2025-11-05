<x-auth-layout>
    <div class="flex flex-col md:flex-row min-h-screen w-screen">
        <!-- Columna izquierda: Formulario de login -->
        <div class="flex flex-col justify-center w-full md:w-1/2 bg-white p-6 md:p-12">
            <!-- Título del sistema -->
            <h1 class="text-center text-display-sm text-primary mb-8">Campos Sport</h1>

            <!-- Imagen de perfil (opcional) -->
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-primary-50">
                    <img src="../img/logo.png" alt="Profile Image" class="w-full h-full object-cover object-center">
                </div>
            </div>

            <!-- Título en rojo -->
            <h1 class="text-center text-heading-lg text-primary mb-6">Iniciar Sesión</h1>

            <!-- Formulario de inicio de sesión -->
            <form method="POST" action="{{ route('login') }}" class="w-full max-w-lg mx-auto">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="text-body-md text-primary-light">Correo electrónico</label>
                    <input id="email" class="block mt-2 w-full border-primary-200 rounded-md p-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-body-md" type="email" name="email" required autofocus autocomplete="username">
                </div>

                <!-- Password -->
                <div class="mt-6">
                    <label for="password" class="text-body-md text-primary-light">Contraseña</label>
                    <input id="password" class="block mt-2 w-full border-primary-200 rounded-md p-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-body-md" type="password" name="password" required autocomplete="current-password">
                </div>

                <!-- Remember Me -->
                <div class="block mt-6">
                    <label for="remember_me" class="inline-flex items-center text-primary-light">
                        <input id="remember_me" type="checkbox" class="rounded border-primary-300 text-primary focus:ring-primary" name="remember">
                        <span class="ml-2 text-body-sm">Recordarme</span>
                    </label>
                </div>

                <!-- Botón de Iniciar Sesión -->
                <div class="flex flex-col items-center justify-between mt-8 space-y-4">
                    <button type="submit" class="w-full sm:w-auto bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary focus:outline-none transition-colors duration-200 text-body-md btn-accessible">
                        Iniciar Sesión
                    </button>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-primary-light underline text-body-sm hover:text-primary transition-colors duration-200 link-accessible">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <!-- Enlace de Registro -->
                <div class="mt-8 text-center">
                    <p class="text-body-sm text-primary-light">¿No tienes cuenta?
                        <a href="{{ route('register') }}" class="text-primary underline hover:text-primary-700 transition-colors duration-200 link-accessible">Regístrate</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Columna derecha: Espacio para la imagen -->
        <div class="hidden md:block md:w-1/2 bg-cover bg-center" style="background-image: url('img/login.jpg');">
            <!-- Asegúrate de que la ruta de la imagen sea correcta -->
        </div>
    </div>
</x-auth-layout>
