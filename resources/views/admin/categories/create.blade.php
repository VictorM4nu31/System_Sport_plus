<x-app-layout>
    <div class="max-w-2xl mx-auto px-2 sm:px-6 lg:px-8 py-2 sm:py-6">
        <div class="bg-white bg-opacity-50 shadow-lg rounded-lg p-3 sm:p-8">
            <h1 class="text-heading-lg text-primary mb-3 sm:mb-6 text-center">Agregar Categoría</h1>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3 sm:space-y-4">
                @csrf
                <!-- Campo de Nombre -->
                <div class="mb-3 sm:mb-4">
                    <label for="name" class="block text-white font-semibold mb-1 sm:mb-2">Nombre</label>
                    <input id="name" name="name" type="text" required
                           class="mt-1 block w-full px-2 sm:px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336] text-sm sm:text-base"
                           placeholder="Escribe el nombre de la categoría">
                    @error('name')
                        <span class="text-error text-body-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botón de Enviar -->
                <div class="flex justify-center mt-4 sm:mt-6">
                    <button type="submit"
                            class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-primary text-white font-semibold rounded-md hover:bg-primary-700 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-200 btn-accessible">
                        Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
