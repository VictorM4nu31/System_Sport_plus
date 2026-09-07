<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal con transparencia y sombra -->
            <div class="bg-white border border-line shadow-xl rounded-lg p-4 sm:p-8">

                <!-- Título de la sección con ícono y color de fondo -->
                <div class="flex flex-col items-center text-center mb-6">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-16 h-16 sm:w-24 sm:h-24 mb-4 rounded-full border-4 border-line" />
                    <h3 class="text-display-sm text-carbon font-bold">Gestión de Categorías</h3>
                </div>

                <!-- Botón de agregar categoría -->
                <div class="flex justify-center mb-6">
                    <a href="{{ route('admin.categories.create') }}"
                       class="px-4 py-2 sm:px-5 sm:py-2 bg-primary text-white rounded-md font-semibold hover:bg-primary-700 transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary btn-accessible">
                       Agregar Categoría
                    </a>
                </div>

                <!-- Tabla de categorías con estilo consistente -->
                <div class="overflow-x-auto">
                    <table class="w-full bg-white bg-opacity-95 rounded-lg shadow-lg mt-4">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th class="px-4 sm:px-6 py-3 border-b-2 border-line text-left text-body-md">Nombre</th>
                                <th class="px-4 sm:px-6 py-3 border-b-2 border-line text-left text-body-md">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr class="hover:bg-paper transition duration-150">
                                    <td class="px-4 sm:px-6 py-3 border-b border-line text-carbon">{{ $category->name }}</td>
                                    <td class="px-4 sm:px-6 py-3 border-b border-line">
                                        <div class="flex items-center gap-2 sm:gap-4">
                                            <!-- Botón de Editar con ícono -->
                                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                               class="flex items-center text-info hover:text-info font-semibold space-x-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712Z"/>
                                                    <path d="M19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z"/>
                                                </svg>
                                                <span>Editar</span>
                                            </a>

                                            <!-- Botón de Eliminar con ícono -->
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="flex items-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex items-center text-error hover:text-red-700 font-semibold space-x-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
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
