<x-app-layout>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white bg-opacity-95 shadow-lg rounded-lg p-4 sm:p-8">
            <h1 class="text-display-sm text-center font-bold text-primary mb-8">Editar Producto Deportivo</h1>

            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" id="productForm">
                @csrf
                @method('PATCH')

                <!-- Información Básica -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-heading-lg font-semibold text-primary mb-4">Información Básica</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="name" class="block text-primary font-semibold mb-2 text-body-md">Nombre del Producto *</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            @error('name')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="mb-4">
                            <label for="sku" class="block text-primary font-semibold mb-2 text-body-md">Código SKU</label>
                            <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            @error('sku')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Marca -->
                        <div class="mb-4">
                            <label for="brand" class="block text-primary font-semibold mb-2 text-body-md">Marca</label>
                            <select id="brand" name="brand"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                <option value="">Seleccionar marca</option>
                                @foreach(['Nike', 'Adidas', 'Puma', 'Under Armour', 'Reebok', 'New Balance', 'Converse', 'Vans', 'Wilson', 'Spalding', 'Otra'] as $brand)
                                    <option value="{{ $brand }}" {{ old('brand', $product->brand) == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                                @endforeach
                            </select>
                            @error('brand')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Modelo -->
                        <div class="mb-4">
                            <label for="model" class="block text-primary font-semibold mb-2 text-body-md">Modelo</label>
                            <input id="model" name="model" type="text" value="{{ old('model', $product->model) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            @error('model')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Precio -->
                        <div class="mb-4">
                            <label for="price" class="block text-primary font-semibold mb-2 text-body-md">Precio (MXN) *</label>
                            <input id="price" name="price" type="number" step="0.01" min="0.01" value="{{ old('price', $product->price) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            @error('price')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div class="mb-4">
                            <label for="stock" class="block text-primary font-semibold mb-2 text-body-md">Stock *</label>
                            <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            @error('stock')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div class="mb-4">
                            <label for="category" class="block text-primary font-semibold mb-2 text-body-md">Categoría *</label>
                            <select id="category" name="category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                <option value="">Seleccionar categoría</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de Deporte -->
                        <div class="mb-4">
                            <label for="sport_type" class="block text-primary font-semibold mb-2 text-body-md">Tipo de Deporte</label>
                            <select id="sport_type" name="sport_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                <option value="">Seleccionar deporte</option>
                                @foreach(['Fútbol', 'Basketball', 'Running', 'Tenis', 'Volleyball', 'Baseball', 'Natación', 'Ciclismo', 'Fitness', 'Casual', 'Multideporte'] as $sport)
                                    <option value="{{ $sport }}" {{ old('sport_type', $product->sport_type) == $sport ? 'selected' : '' }}>{{ $sport }}</option>
                                @endforeach
                            </select>
                            @error('sport_type')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Características del Producto -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-heading-lg font-semibold text-primary mb-4">Características del Producto</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Género -->
                        <div class="mb-4">
                            <label for="gender" class="block text-primary font-semibold mb-2 text-body-md">Género</label>
                            <select id="gender" name="gender"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                <option value="">Seleccionar género</option>
                                @foreach(['hombre', 'mujer', 'unisex'] as $gender)
                                    <option value="{{ $gender }}" {{ old('gender', $product->gender) == $gender ? 'selected' : '' }}>
                                        {{ ucfirst($gender) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gender')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Material -->
                        <div class="mb-4">
                            <label for="material" class="block text-primary font-semibold mb-2 text-body-md">Material</label>
                            <input id="material" name="material" type="text" value="{{ old('material', $product->material) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            @error('material')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Peso -->
                        <div class="mb-4">
                            <label for="weight" class="block text-primary font-semibold mb-2 text-body-md">Peso (kg)</label>
                            <input id="weight" name="weight" type="number" step="0.01" min="0" value="{{ old('weight', $product->weight) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            @error('weight')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Producto Destacado -->
                        <div class="mb-4 flex items-center">
                            <input id="is_featured" name="is_featured" type="checkbox" value="1"
                                {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                class="h-4 w-4 text-[#801336] focus:ring-[#801336] border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-primary font-semibold text-body-md">Producto Destacado</label>
                        </div>
                    </div>
                </div>

                <!-- Variantes -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-heading-lg font-semibold text-primary mb-4">Variantes del Producto</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tallas -->
                        <div class="mb-4">
                            <label class="block text-primary font-semibold mb-2 text-body-md">Tallas Disponibles</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', '22', '23', '24', '25', '26', '27', '28', '29', '30'] as $size)
                                <label class="flex items-center">
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                        {{ in_array($size, old('sizes', $product->sizes ?? [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-[#801336] focus:ring-[#801336] border-gray-300 rounded">
                                    <span class="ml-1 text-body-sm text-gray-700">{{ $size }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('sizes')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Colores -->
                        <div class="mb-4">
                            <label class="block text-primary font-semibold mb-2 text-body-md">Colores Disponibles</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['Negro', 'Blanco', 'Rojo', 'Azul', 'Verde', 'Amarillo', 'Rosa', 'Gris', 'Naranja'] as $color)
                                <label class="flex items-center">
                                    <input type="checkbox" name="colors[]" value="{{ $color }}"
                                        {{ in_array($color, old('colors', $product->colors ?? [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-[#801336] focus:ring-[#801336] border-gray-300 rounded">
                                    <span class="ml-1 text-body-sm text-gray-700">{{ $color }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('colors')
                                <span class="text-error text-body-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Descripción e Imagen -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-heading-lg font-semibold text-primary mb-4">Descripción e Imagen</h2>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="description" class="block text-primary font-semibold mb-2 text-body-md">Descripción *</label>
                        <textarea id="description" name="description" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <span class="text-error text-body-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Imagen -->
                    <div class="mb-4">
                        <label for="image" class="block text-primary font-semibold mb-2 text-body-md">Imagen del Producto</label>
                        <input id="image" name="image" type="file" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                        <p class="text-body-sm text-gray-600 mt-1">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 2MB</p>
                        @if ($product->image)
                            <div class="mt-4">
                                <p class="text-body-sm text-gray-600 mb-2">Imagen actual:</p>
                                <img src="{{ asset('storage/products/' . $product->image) }}"
                                     class="h-32 w-32 object-cover rounded-md shadow border"
                                     alt="{{ $product->name }}">
                            </div>
                        @endif
                        @error('image')
                            <span class="text-error text-body-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Especificaciones Técnicas -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-heading-lg font-semibold text-primary mb-4">Especificaciones Técnicas</h2>
                    <div id="specifications-container">
                        @if($product->specifications && count($product->specifications) > 0)
                            @foreach($product->specifications as $key => $value)
                            <div class="specification-row grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
                                <input type="text" name="spec_keys[]" value="{{ $key }}" placeholder="Característica"
                                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                <div class="flex">
                                    <input type="text" name="spec_values[]" value="{{ $value }}" placeholder="Valor"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                    <button type="button" onclick="this.parentElement.parentElement.remove()"
                                        class="px-3 py-2 bg-red-500 text-white rounded-r-md hover:bg-red-600">×</button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="specification-row grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
                                <input type="text" name="spec_keys[]" placeholder="Característica (ej: Suela)"
                                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                <input type="text" name="spec_values[]" placeholder="Valor (ej: Goma antideslizante)"
                                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-specification"
                        class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        + Agregar Especificación
                    </button>
                </div>

                <!-- Botones -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('admin.products.index') }}"
                        class="px-6 py-3 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 text-body-md">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-semibold rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary shadow-lg text-body-md">
                        Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Agregar especificaciones dinámicamente
        document.getElementById('add-specification').addEventListener('click', function() {
            const container = document.getElementById('specifications-container');
            const newRow = document.createElement('div');
            newRow.className = 'specification-row grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2';
            newRow.innerHTML = `
                <input type="text" name="spec_keys[]" placeholder="Característica"
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                <div class="flex">
                    <input type="text" name="spec_values[]" placeholder="Valor"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                    <button type="button" onclick="this.parentElement.parentElement.remove()"
                        class="px-3 py-2 bg-red-500 text-white rounded-r-md hover:bg-red-600">×</button>
                </div>
            `;
            container.appendChild(newRow);
        });

        // Validación del formulario
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const price = parseFloat(document.getElementById('price').value);
            const stock = parseInt(document.getElementById('stock').value);

            if (price <= 0) {
                e.preventDefault();
                alert('El precio debe ser mayor a 0');
                return false;
            }

            if (stock < 0) {
                e.preventDefault();
                alert('El stock no puede ser negativo');
                return false;
            }
        });
    </script>
</x-app-layout>
