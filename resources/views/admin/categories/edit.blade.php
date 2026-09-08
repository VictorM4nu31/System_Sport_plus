<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white bg-opacity-95 shadow-lg rounded-lg p-4 sm:p-8">
            <h1 class="text-heading-lg text-primary mb-6 text-center">Editar Categoría</h1>
            <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" class="flex flex-col gap-4">
                @csrf
                @method('PATCH')

                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-carbon font-semibold">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ $category->name }}" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-carbon focus:border-carbon"
                           placeholder="Nombre de la categoría">
                    @error('name')
                        <span class="text-error text-body-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row justify-center gap-3 mt-6">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] rounded-md border border-line text-carbon hover:bg-paper transition w-full sm:w-auto">Cancelar</a>
                    <button type="submit"
                            class="px-4 py-2 min-h-[44px] bg-primary text-white font-semibold rounded-md hover:bg-primary-700 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary btn-accessible w-full sm:w-auto">
                        Actualizar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
