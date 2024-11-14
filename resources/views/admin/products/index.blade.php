<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal con transparencia y sombra -->
            <div class="bg-[#FFFFFF] bg-opacity-50 shadow-xl rounded-lg p-8">
                
                <!-- Contenedor para el logo centrado -->
                <div class="flex justify-center mb-4">
                    <img src="/img/logo.jpeg" alt="Logo" class="w-24 h-24 rounded-full border-4 border-[#801336]">
                </div>
                
                <!-- Título centrado -->
                <h3 class="text-3xl font-bold text-center text-[#FFFFFF] mb-6">Gestión de Productos</h3>
                
                <!-- Botón de agregar producto centrado -->
                <div class="flex justify-center mb-6">
                    <a href="{{ route('admin.products.create') }}" 
                       class="px-5 py-2 bg-[#801336] text-white rounded-md font-semibold hover:bg-[#9b1a3e] transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Agregar Producto
                    </a>
                </div>

                <!-- Botones de gestionar categorías y órdenes centrados -->
                <div class="flex justify-center space-x-4 mb-6">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="px-5 py-2 bg-[#801336] text-white rounded-md font-semibold hover:bg-[#9b1a3e] transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Gestionar Categorías
                    </a>
                    <a href="{{ route('admin.orders.index') }}" 
                       class="px-5 py-2 bg-[#801336] text-white rounded-md font-semibold hover:bg-[#9b1a3e] transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#801336]">
                        Gestionar Órdenes
                    </a>
                </div>

                <!-- Tabla de productos -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-95 rounded-lg shadow-lg mt-4">
                        <thead>
                            <tr class="bg-[#801336] text-white">
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left">Imagen</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left">Nombre</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left">Precio</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left">Stock</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="hover:bg-[#E7E3C4] transition duration-150">
                                    <td class="px-6 py-3 border-b border-[#801336]">
                                        @if($product->image)
                                            <img src="{{'/storage/'. $product->image}}" class="h-16 w-16 object-cover rounded-md shadow-sm" alt="{{ $product->name }}">
                                        @else
                                            <p class="text-[#801336] italic">Sin imagen</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $product->name }}</td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $product->price }}</td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $product->stock }}</td>
                                    <td class="px-6 py-3 border-b border-[#801336]">
                                        <div class="flex items-center gap-4">
                                            <!-- Botón de Editar con ícono -->
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="flex items-center text-[#6A92C7] hover:text-[#6A92C7] font-semibold space-x-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712Z"/>
                                                    <path d="M19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z"/>
                                                </svg>
                                                <span>Editar</span>
                                            </a>

                                            <!-- Botón de Eliminar con ícono -->
                                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="flex items-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex items-center text-[#C5283D] hover:text-red-700 font-semibold space-x-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>Eliminar</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
