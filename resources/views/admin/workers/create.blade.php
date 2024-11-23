<x-app-layout>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con opacidad y sombra -->
        <div class="bg-white bg-opacity-50 shadow-lg rounded-lg p-8">
            <h1 class="text-2xl text-center font-bold text-gray-800 text-[#FFFFFF] mb-10">Registrar Trabajador</h1>
            <form method="POST" action="{{ route('admin.workers.store') }}">
                @csrf
                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-[#FFFFFF] font-semibold">Nombre</label>
                    <input id="name" name="name" type="text" required 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#6A92C7] focus:border-[#6A92C7]">
                    @error('name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Email -->
                <div class="mb-4">
                    <label for="email" class="block text-[#FFFFFF] font-semibold">Correo Electrónico</label>
                    <input id="email" name="email" type="email" required 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#6A92C7] focus:border-[#6A92C7]">
                    @error('email')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Contraseña -->
                <div class="mb-4">
                    <label for="password" class="block text-[#FFFFFF] font-semibold">Contraseña</label>
                    <input id="password" name="password" type="password" required 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#6A92C7] focus:border-[#6A92C7]">
                    @error('password')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Confirmación de Contraseña -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-[#FFFFFF] font-semibold">Confirmar Contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#6A92C7] focus:border-[#6A92C7]">
                </div>

                <!-- Botón de Enviar con estilo -->
               <!-- Contenedor para centrar el botón -->
                <div class="flex justify-center">
                    <button type="submit" 
                    class="px-4 py-2 bg-[#801336] text-white font-semibold rounded-md hover:bg-[#9b1a3e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Registrar
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>

