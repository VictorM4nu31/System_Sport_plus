<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Editar Producto</h1>
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Nombre -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nombre</label>
                <input id="name" name="name" type="text" value="{{ $product->name }}" required class="mt-1 block w-full">
                @error('name')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Precio -->
            <div class="mb-4">
                <label for="price" class="block text-gray-700">Precio</label>
                <input id="price" name="price" type="number" step="0.01" value="{{ $product->price }}" required class="mt-1 block w-full">
                @error('price')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Descripción -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Descripción</label>
                <textarea id="description" name="description" required class="mt-1 block w-full">{{ $product->description }}</textarea>
                @error('description')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Stock -->
            <div class="mb-4">
                <label for="stock" class="block text-gray-700">Stock</label>
                <input id="stock" name="stock" type="number" value="{{ $product->stock }}" required class="mt-1 block w-full">
                @error('stock')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Categoría -->
            <select id="category" name="category_id" required class="mt-1 block w-full">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>

            <!-- Imagen -->
            <div class="mb-4">
                <label for="image" class="block text-gray-700">Imagen</label>
                <input id="image" name="image" type="file" class="mt-1 block w-full">
                @error('image')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
                @if ($product->image)
                    <img src="{{ asset('storage/products/' . $product->image) }}" class="mt-4 h-20 w-20" alt="{{ $product->name }}">
                @endif
            </div>

            <!-- Botón de Enviar -->
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Actualizar Producto</button>
            </div>
        </form>
    </div>
</x-app-layout>