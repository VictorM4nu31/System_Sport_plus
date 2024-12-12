<x-guest-layout>
    <div class="flex min-h-screen w-screen">
        
        <!-- Columna izquierda: Formulario de login -->
        <div class="flex flex-col justify-center w-1/2" style="background-color: #ffffff; padding: 4rem;"> <!-- Aplicado color de fondo en línea -->
            <!-- Título del sistema -->
            <h1 class="text-center text-3xl font-bold" style="color: #333333; margin-bottom: 2rem;">Campos Sport</h1>

            <!-- Imagen de perfil (opcional) -->
            <div class="flex justify-center mb-8">
                <div class="w-24 h-24 rounded-full overflow-hidden" style="background-color: #e0e0e0;">
                    <img src="../img/logo.png" alt="Profile Image" class="w-full h-full object-cover " style="object-position: center;">
                </div>
            </div>

            <!-- Título en rojo -->
            <h1 class="text-center text-3xl font-bold mb-8" style="color: #D40000;">Iniciar Sesión</h1>

            <!-- Formulario de inicio de sesión -->
            <form method="POST" action="{{ route('login') }}" class="w-full max-w-lg mx-auto">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" style="color: #666666;">Correo electrónico</label>
                    <input id="email" class="block mt-2 w-full border-gray-300 rounded-md" type="email" name="email" required autofocus autocomplete="username" style="border-color: #cccccc; padding: 0.5rem; border-radius: 0.375rem; width: 100%;">
                </div>

                <!-- Password -->
                <div class="mt-6">
                    <label for="password" style="color: #666666;">Contraseña</label>
                    <input id="password" class="block mt-2 w-full border-gray-300 rounded-md" type="password" name="password" required autocomplete="current-password" style="border-color: #cccccc; padding: 0.5rem; border-radius: 0.375rem; width: 100%;">
                </div>

                <!-- Remember Me -->
                <div class="block mt-6">
                    <label for="remember_me" class="inline-flex items-center" style="color: #666666;">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm" name="remember">
                        <span class="ml-2 text-sm">Recordarme</span>
                    </label>
                </div>

                <!-- Botón de Iniciar Sesión en rojo -->
                <div class="flex flex-col items-center justify-between mt-8 space-y-4">
                    <button type="submit" style="background-color: #D40000; color: #ffffff; font-weight: bold; padding: 0.5rem 1rem; border-radius: 0.375rem;">
                        Iniciar
                    </button>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: #666666; text-decoration: underline;">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <!-- Enlace de Registro -->
                <div class="mt-8 text-center">
                    <p class="text-sm" style="color: #666666;">¿No tienes cuenta?
                        <a href="{{ route('register') }}" style="color: #D40000; text-decoration: underline;">Regístrate</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Columna derecha: Espacio para la imagen -->
        <div class="w-1/2 bg-cover bg-center" style="background-image: url('img/login.jpg'); min-height: 100vh;">
            <!-- Asegúrate de que la ruta de la imagen sea correcta -->
        </div>
    </div>
</x-guest-layout>
