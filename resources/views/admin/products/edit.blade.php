<x-app-layout>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white bg-opacity-50 shadow-lg rounded-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Editar Producto</h1>
            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-white font-semibold">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ $product->name }}" required 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#801336] focus:border-[#801336]"
                           placeholder="Nombre del producto">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Precio -->
                <div class="mb-4">
                    <label for="price" class="block text-white font-semibold">Precio</label>
                    <input id="price" name="price" type="number" step="0.01" value="{{ $product->price }}" required 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#801336] focus:border-[#801336]"
                           placeholder="Precio del producto">
                    @error('price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="mb-4">
                    <label for="description" class="block text-white font-semibold">Descripción</label>
                    <textarea id="description" name="description" required 
                              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#801336] focus:border-[#801336]"
                              placeholder="Descripción del producto">{{ $product->description }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Stock -->
                <div class="mb-4">
                    <label for="stock" class="block text-white font-semibold">Stock</label>
                    <input id="stock" name="stock" type="number" value="{{ $product->stock }}" required 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#801336] focus:border-[#801336]"
                           placeholder="Cantidad de stock">
                    @error('stock')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Categoría -->
                <div class="mb-4">
                    <label for="category" class="block text-white font-semibold">Categoría</label>
                    <select id="category" name="category_id" required 
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#801336] focus:border-[#801336]">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Imagen -->
                <div class="mb-4">
                    <label for="image" class="block text-white font-semibold">Imagen</label>
                    <input id="image" name="image" type="file" 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#801336] focus:border-[#801336]">
                    @error('image')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    @if ($product->image)
                        <img src="{{ asset('storage/products/' . $product->image) }}" class="mt-4 h-20 w-20 rounded-md shadow" alt="{{ $product->name }}">
                    @endif
                </div>

                <!-- Botón de Enviar -->
                <div class="flex justify-center mt-6">
                    <button type="submit" 
                            class="px-4 py-2 bg-[#801336] text-white font-semibold rounded-md hover:bg-[#9b1a3e] shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
