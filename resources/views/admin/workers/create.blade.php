<x-app-layout>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con opacidad y sombra -->
        <div class="bg-white bg-opacity-95 shadow-lg rounded-lg p-8">
            <h1 class="text-display-sm text-primary font-bold text-center mb-6">Registrar Trabajador</h1>
            <form method="POST" action="{{ route('admin.workers.store') }}">
                @csrf
                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-primary text-body-md font-medium mb-2">Nombre</label>
                    <input id="name" name="name" type="text" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]"
                        placeholder="Nombre completo del trabajador">
                    @error('name')
                        <span class="text-error text-body-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Email -->
                <div class="mb-4">
                    <label for="email" class="block text-primary text-body-md font-medium mb-2">Correo Electrónico</label>
                    <input id="email" name="email" type="email" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]"
                        placeholder="correo@ejemplo.com">
                    @error('email')
                        <span class="text-error text-body-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Contraseña -->
                <div class="mb-4">
                    <label for="password" class="block text-primary text-body-md font-medium mb-2">Contraseña</label>
                    <input id="password" name="password" type="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]"
                        placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <span class="text-error text-body-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Confirmación de Contraseña -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-primary text-body-md font-medium mb-2">Confirmar Contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]"
                        placeholder="Repetir la contraseña">
                </div>

                <!-- Botones -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('admin.workers.index') }}"
                        class="px-6 py-3 bg-gray-500 text-white text-body-md font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white text-body-md font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary shadow-lg btn-accessible">
                        Registrar Trabajador
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>

