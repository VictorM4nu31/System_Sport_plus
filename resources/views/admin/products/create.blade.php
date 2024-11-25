<x-app-layout>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con opacidad y sombra -->
        <div class="bg-white bg-opacity-50 shadow-lg rounded-lg p-8">
            <h1 class="text-2xl text-center font-bold text-gray-800 mb-6">Agregar Producto</h1>
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-white font-semibold">Nombre</label>
                    <input id="name" name="name" type="text" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#C72C41] focus:border-[#C72C41]">
                    @error('name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Precio -->
                <div class="mb-4">
                    <label for="price" class="block text-white font-semibold">Precio (MXN) </label>
                    <input id="price" name="price" type="number" step="0.01" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                    @error('price')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Descripción -->
                <div class="mb-4">
                    <label for="description" class="block text-white font-semibold">Descripción</label>
                    <textarea id="description" name="description" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"></textarea>
                    @error('description')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Stock -->
                <div class="mb-4">
                    <label for="stock" class="block text-white font-semibold">Stock</label>
                    <input id="stock" name="stock" type="number" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                    @error('stock')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo de Categoría -->
                <div class="mb-4">
                    <label for="category" class="block text-white font-semibold">Categoría</label>
                    <select id="category" name="category_id" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Campo de Imagen -->
                <div class="mb-4">
                    <label for="image" class="block text-white font-semibold">Imagen</label>
                    <input id="image" name="image" type="file"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                    @error('image')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botón de Enviar centrado -->
                <div class="flex justify-center">
                    <button type="submit"
                        class="px-4 py-2 bg-[#801336] text-white font-semibold rounded-md hover:bg-[#9b1a3e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
