<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Registrar Trabajador</h1>
        <form method="POST" action="{{ route('admin.workers.store') }}">
            @csrf
            <!-- Campo de Nombre -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nombre</label>
                <input id="name" name="name" type="text" required class="mt-1 block w-full" autofocus>
                @error('name')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <!-- Campo de Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Correo Electrónico</label>
                <input id="email" name="email" type="email" required class="mt-1 block w-full">
                @error('email')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <!-- Campo de Contraseña -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Contraseña</label>
                <input id="password" name="password" type="password" required class="mt-1 block w-full">
                @error('password')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <!-- Campo de Confirmación de Contraseña -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700">Confirmar Contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full">
            </div>
            <!-- Botón de Enviar -->
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Registrar</button>
            </div>
        </form>
    </div>
</x-app-layout>
