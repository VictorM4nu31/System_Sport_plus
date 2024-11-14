<x-app-layout>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white bg-opacity-50 shadow-lg rounded-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Agregar Categoría</h1>
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-white font-semibold">Nombre</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#801336] focus:border-[#801336]"
                           placeholder="Escribe el nombre de la categoría">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botón de Enviar -->
                <div class="flex justify-center mt-6">
                    <button type="submit" 
                            class="px-4 py-2 bg-[#801336] text-white font-semibold rounded-md hover:bg-[#9b1a3e] shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
