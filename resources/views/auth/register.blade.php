<x-guest-layout>
    <!-- Contenedor dividido en dos columnas con Flexbox -->
    <div class="flex flex-col lg:flex-row min-h-screen w-full">

        <!-- Columna izquierda: Espacio para la imagen -->
        <div class="hidden lg:block lg:w-1/2 bg-cover bg-center" style="background-image: url('img/registro.jpg');">
            <!-- Aquí va la imagen de fondo -->
        </div>

        <!-- Columna derecha: Formulario de registro -->
        <div class="flex flex-col justify-center w-full lg:w-1/2 bg-white p-8 lg:p-16">
            <!-- Título del formulario en rojo -->
            <h1 class="text-center text-3xl font-bold text-red-600 mb-8">Registro</h1>

            <!-- Formulario de registro -->
            <form method="POST" action="{{ route('register') }}" class="w-full max-w-lg mx-auto">
                @csrf

                <!-- Nombre -->
                <div class="mb-4">
                    <label for="name" class="text-gray-600">Nombre</label>
                    <input id="name" class="block mt-2 w-full border-gray-300 rounded-md p-2 focus:ring-red-500 focus:border-red-500"
                           type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                </div>

                <!-- Correo electrónico -->
                <div class="mb-4">
                    <label for="email" class="text-gray-600">Correo electrónico</label>
                    <input id="email" class="block mt-2 w-full border-gray-300 rounded-md p-2 focus:ring-red-500 focus:border-red-500"
                           type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label for="password" class="text-gray-600">Contraseña</label>
                    <input id="password" class="block mt-2 w-full border-gray-300 rounded-md p-2 focus:ring-red-500 focus:border-red-500"
                           type="password" name="password" required autocomplete="new-password">
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mb-4">
                    <label for="password_confirmation" class="text-gray-600">Confirmar Contraseña</label>
                    <input id="password_confirmation" class="block mt-2 w-full border-gray-300 rounded-md p-2 focus:ring-red-500 focus:border-red-500"
                           type="password" name="password_confirmation" required autocomplete="new-password">
                </div>

                <!-- Botón de registro en rojo y enlace de inicio de sesión -->
                <div class="flex flex-col items-center justify-center mt-4 space-y-4">
                    <!-- Botón de registro en rojo -->
                    <button type="submit" class="bg-red-600 text-white font-bold py-2 px-6 rounded-md hover:bg-red-700 transition duration-200">
                        Registrarse
                    </button>

                    <!-- Texto debajo del botón, centrado -->
                    <a href="{{ route('login') }}" class="text-gray-600 underline text-sm hover:text-gray-800">
                        ¿Ya tienes una cuenta? Inicia sesión
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
