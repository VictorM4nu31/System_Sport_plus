<x-app-layout>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <h1 class="text-2xl font-semibold mb-6">Editar Trabajador</h1>
    <form method="POST" action="{{ route('admin.workers.update', $worker->id) }}">
        @csrf
        @method('PATCH')

        <!-- Campo de Nombre -->
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Nombre</label>
            <input id="name" name="name" type="text" value="{{ $worker->name }}" required
                class="mt-1 block w-full">
            @error('name')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <!-- Campo de Email -->
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Correo Electrónico</label>
            <input id="email" name="email" type="email" value="{{ $worker->email }}" required
                class="mt-1 block w-full">
            @error('email')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <!-- Botón de Actualizar -->
        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Actualizar</button>
        </div>
    </form>
</div>
</x-app-layout>