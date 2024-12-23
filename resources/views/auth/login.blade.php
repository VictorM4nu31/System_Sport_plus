<x-guest-layout>
    <div class="flex flex-col md:flex-row min-h-screen w-screen">
        <!-- Columna izquierda: Formulario de login -->
        <div class="flex flex-col justify-center w-full md:w-1/2 bg-white p-6 md:p-12">
            <!-- Título del sistema -->
            <h1 class="text-center text-3xl font-bold text-gray-800 mb-8">Campos Sport</h1>

            <!-- Imagen de perfil (opcional) -->
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200">
                    <img src="../img/logo.png" alt="Profile Image" class="w-full h-full object-cover object-center">
                </div>
            </div>

            <!-- Título en rojo -->
            <h1 class="text-center text-2xl md:text-3xl font-bold text-red-600 mb-6">Iniciar Sesión</h1>

            <!-- Formulario de inicio de sesión -->
            <form method="POST" action="{{ route('login') }}" class="w-full max-w-lg mx-auto">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="text-gray-600">Correo electrónico</label>
                    <input id="email" class="block mt-2 w-full border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-red-500 focus:outline-none" type="email" name="email" required autofocus autocomplete="username">
                </div>

                <!-- Password -->
                <div class="mt-6">
                    <label for="password" class="text-gray-600">Contraseña</label>
                    <input id="password" class="block mt-2 w-full border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-red-500 focus:outline-none" type="password" name="password" required autocomplete="current-password">
                </div>

                <!-- Remember Me -->
                <div class="block mt-6">
                    <label for="remember_me" class="inline-flex items-center text-gray-600">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-red-600 focus:ring-red-500" name="remember">
                        <span class="ml-2 text-sm">Recordarme</span>
                    </label>
                </div>

                <!-- Botón de Iniciar Sesión -->
                <div class="flex flex-col items-center justify-between mt-8 space-y-4">
                    <button type="submit" class="w-full sm:w-auto bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500">
                        Iniciar
                    </button>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-gray-600 underline text-sm hover:text-gray-800">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <!-- Enlace de Registro -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">¿No tienes cuenta?
                        <a href="{{ route('register') }}" class="text-red-600 underline hover:text-red-800">Regístrate</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Columna derecha: Espacio para la imagen -->
        <div class="hidden md:block md:w-1/2 bg-cover bg-center" style="background-image: url('img/login.jpg');">
            <!-- Asegúrate de que la ruta de la imagen sea correcta -->
        </div>
    </div>
</x-guest-layout>
