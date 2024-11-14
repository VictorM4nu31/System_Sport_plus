<x-app-layout>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con opacidad y sombra -->
        <div class="bg-white bg-opacity-50 shadow-lg rounded-lg p-8">
            <!-- Título centrado -->
            <h1 class="text-2xl text-center font-bold text-gray-800 mb-6">Editar Trabajador</h1>

            <!-- Formulario de edición -->
            <form method="POST" action="{{ route('admin.workers.update', $worker->id) }}">
                @csrf
                @method('PATCH')

                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-white font-semibold">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ $worker->name }}" required 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#C72C41] focus:border-[#C72C41]">
                    @error('name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Email -->
                <div class="mb-4">
                    <label for="email" class="block text-white font-semibold">Correo Electrónico</label>
                    <input id="email" name="email" type="email" value="{{ $worker->email }}" required 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#C72C41] focus:border-[#C72C41]">
                    @error('email')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botón de Actualizar centrado -->
                <div class="flex justify-center">
                    <button type="submit" 
                        class="px-4 py-2 bg-[#801336] text-white font-semibold rounded-md hover:bg-[#9b1a3e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
