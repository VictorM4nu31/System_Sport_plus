<x-guest-layout>
    <!-- Contenedor dividido en dos columnas con Flexbox -->
    <div class="flex min-h-screen w-full">
        
        <!-- Columna izquierda: Espacio para la imagen -->
        <div class="w-full lg:w-1/2 bg-cover bg-center" style="background-image: url('img/registro.jpg'); min-height: 100vh;">
            <!-- Aquí va la imagen de fondo -->
        </div>

        <!-- Columna derecha: Formulario de registro -->
        <div class="flex flex-col justify-center w-full lg:w-1/2 bg-white p-16">
            <!-- Título del formulario en rojo -->
            <h1 class="text-center text-3xl font-bold mb-8" style="color: #D40000;">Register</h1>

            <!-- Formulario de registro -->
            <form method="POST" action="{{ route('register') }}" class="w-full max-w-lg mx-auto">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" style="color: #666666;">Name</label>
                    <input id="name" class="block mt-2 w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                           type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                           style="border-color: #cccccc; padding: 0.5rem; border-radius: 0.375rem; width: 100%;">
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" style="color: #666666;">Email</label>
                    <input id="email" class="block mt-2 w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                           type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                           style="border-color: #cccccc; padding: 0.5rem; border-radius: 0.375rem; width: 100%;">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" style="color: #666666;">Password</label>
                    <input id="password" class="block mt-2 w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                           type="password" name="password" required autocomplete="new-password"
                           style="border-color: #cccccc; padding: 0.5rem; border-radius: 0.375rem; width: 100%;">
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" style="color: #666666;">Confirm Password</label>
                    <input id="password_confirmation" class="block mt-2 w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                           type="password" name="password_confirmation" required autocomplete="new-password"
                           style="border-color: #cccccc; padding: 0.5rem; border-radius: 0.375rem; width: 100%;">
                </div>

                <!-- Botón de registro en rojo y enlace de inicio de sesión -->
                <div class="flex flex-col items-center justify-center mt-4 space-y-4">
                    <!-- Botón de registro en rojo -->
                    <button type="submit" style="background-color: #D40000; color: #ffffff; font-weight: bold; padding: 0.5rem 1rem; border-radius: 0.375rem;">
                        Register
                    </button>
                    
                    <!-- Texto debajo del botón, centrado -->
                    <a href="{{ route('login') }}" style="color: #666666; text-decoration: underline;">
                        Already registered?
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
