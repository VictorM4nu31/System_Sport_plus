<x-app-layout>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con opacidad y sombra -->
        <div class="bg-white bg-opacity-95 shadow-lg rounded-lg p-8">
            <!-- Título centrado -->
            <h1 class="text-display-sm text-primary font-bold text-center mb-6">Editar Trabajador</h1>

            <!-- Formulario de edición -->
            <form method="POST" action="{{ route('admin.trabajadores.update', $worker->id) }}">
                @csrf
                @method('PATCH')

                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-primary text-body-md font-medium mb-2">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ $worker->name }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                    @error('name')
                        <span class="text-error text-body-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Email -->
                <div class="mb-6">
                    <label for="email" class="block text-primary text-body-md font-medium mb-2">Correo Electrónico</label>
                    <input id="email" name="email" type="email" value="{{ $worker->email }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                    @error('email')
                        <span class="text-error text-body-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('admin.trabajadores.index') }}"
                        class="px-6 py-3 bg-gray-500 text-white text-body-md font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white text-body-md font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary shadow-lg btn-accessible">
                        Actualizar Trabajador
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
