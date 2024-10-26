<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Lista de Deseos</h1>

        @if (session('wishlist') && count(session('wishlist')) > 0)
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2">Producto</th>
                        <th class="px-6 py-3 border-b-2">Precio</th>
                        <th class="px-6 py-3 border-b-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($wishlist as $id => $details)
                        <tr>
                            <td class="px-6 py-3 border-b">{{ $details['name'] }}</td>
                            <td class="px-6 py-3 border-b">${{ number_format($details['price'], 2) }}</td>
                            <td class="px-6 py-3 border-b">
                                <form action="{{ route('usuario.wishlist.remove', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No tienes productos en la lista de deseos.</p>
        @endif
    </div>
</x-app-layout>
