<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Gestión de Trabajadores</h3>
                <a href="{{ route('admin.workers.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">Agregar
                    Trabajador</a>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
                    <h1 class="text-2xl font-semibold mb-6">Listado de Trabajadores</h1>
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Nombre</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Correo</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workers as $worker)
                                <tr>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $worker->name }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $worker->email }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">
                                        <a href="{{ route('admin.workers.edit', $worker->id) }}"
                                            class="text-blue-600 hover:text-blue-900">Editar</a> |
                                        <form action="{{ route('admin.workers.destroy', $worker->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
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
