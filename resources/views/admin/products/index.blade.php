<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Gestión de Productos</h3>
                <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">Agregar Producto</a>
                <table class="min-w-full bg-white mt-4">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Nombre</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Precio</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Stock</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $product->name }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $product->price }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $product->stock }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-900">Editar</a> |
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
