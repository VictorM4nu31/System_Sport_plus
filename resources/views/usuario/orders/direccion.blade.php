<x-app-layout>
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 py-8 bg-gray-50 shadow-md">
        @if (session('error'))
            <x-alert type="error" dismissible="true" class="mb-4">
                {{ session('error') }}
            </x-alert>
        @endif

        @if (session('success'))
            <x-alert type="success" dismissible="true" class="mb-4">
                {{ session('success') }}
            </x-alert>
        @endif

        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Agrega un domicilio</h1>
        <form method="POST" action="{{ route('usuario.addresses.store') }}" onsubmit="return confirmSubmit()">
            @csrf

            <!-- Nombre y apellido -->
            <div class="mb-4">
                <label for="full_name" class="block text-gray-700 font-medium text-sm">Nombre y apellido</label>
                <input id="full_name" name="full_name" type="text" required
                    class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                    placeholder="Tal cual figure en el INE o IFE">
            </div>

            <!-- Código Postal -->
            <div class="mb-4">
                <label for="postal_code" class="block text-gray-700 font-medium text-sm">Código postal</label>
                <input id="postal_code" name="postal_code" type="text" required
                    class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                    placeholder="Ingresa tu código postal" oninput="fetchAddressData()">
                <a href="https://micodigopostal.org/" class="text-blue-500 text-xs hover:underline mt-1 inline-block">No
                    sé mi código</a>
            </div>

            <!-- Estado y Municipio/Alcaldía -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="state" class="block text-gray-700 font-medium text-sm">Estado</label>
                    <input id="state" name="state" type="text" readonly
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-md bg-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="municipality" class="block text-gray-700 font-medium text-sm">Municipio/Alcaldía</label>
                    <select id="municipality" name="municipality" required
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200 bg-white">
                        <option value="" disabled selected>Selecciona</option>
                    </select>
                </div>
            </div>

            <!-- Colonia -->
            <div class="mb-4">
                <label for="neighborhood" class="block text-gray-700 font-medium text-sm">Colonia</label>
                <input id="neighborhood" name="neighborhood" type="text" required
                    class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                    placeholder="Escribe tu colonia o localidad">
            </div>

            <!-- Calle y Número -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="col-span-2">
                    <label for="street" class="block text-gray-700 font-medium text-sm">Calle</label>
                    <input id="street" name="street" type="text" required
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label for="number" class="block text-gray-700 font-medium text-sm">Número</label>
                    <input id="number" name="number" type="text"
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                    <div class="flex items-center mt-1">
                        <input type="checkbox" id="no_number" name="no_number" class="mr-1">
                        <label for="no_number" class="text-gray-600 text-xs">Sin número</label>
                    </div>
                </div>
            </div>

            <!-- Número interior/Depto -->
            <div class="mb-4">
                <label for="interior_number" class="block text-gray-700 font-medium text-sm">Nº interior/Depto
                    (opcional)</label>
                <input id="interior_number" name="interior_number" type="text"
                    class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200">
            </div>

            <!-- Teléfono de contacto -->
            <div class="mb-4">
                <label for="contact_phone" class="block text-gray-700 font-medium text-sm">Teléfono de contacto</label>
                <input id="contact_phone" name="contact_phone" type="text" required
                    class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200">
            </div>

            <!-- Indicaciones adicionales -->
            <div class="mb-4">
                <label for="additional_instructions" class="block text-gray-700 font-medium text-sm">Indicaciones
                    adicionales</label>
                <textarea id="additional_instructions" name="additional_instructions"
                    class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                    placeholder="Descripción de la fachada, puntos de referencia, etc."></textarea>
            </div>

            <!-- Botón de Guardar -->
            <button type="submit"
                class="bg-primary text-white font-semibold p-2 rounded-md w-full hover:bg-primary-700 transition duration-300 btn-accessible">Guardar</button>
        </form>
    </div>

    <script>
        function confirmSubmit() {
            return confirm('¿Estás seguro de que esta dirección es correcta? No podrás editarla después.');
        }

        async function fetchAddressData() {
            const postalCode = document.getElementById('postal_code').value;
            if (postalCode.length === 5) {
                try {
                    const response = await fetch(`https://api.zippopotam.us/MX/${postalCode}`);
                    if (!response.ok) {
                        throw new Error('Error al obtener los datos de la dirección');
                    }
                    const data = await response.json();
                    const state = data.places[0]["state"];
                    const municipalities = data.places.map(place => place["place name"]);

                    // Actualiza el estado
                    document.getElementById('state').value = state;

                    // Limpia y actualiza el select de municipios/alcaldías
                    const municipalitySelect = document.getElementById('municipality');
                    municipalitySelect.innerHTML =
                        '<option value="" disabled selected>Selecciona</option>';
                    municipalities.forEach(municipality => {
                        const option = document.createElement('option');
                        option.value = municipality;
                        option.textContent = municipality;
                        municipalitySelect.appendChild(option);
                    });
                } catch (error) {
                    console.error(error);
                    alert('No se pudo cargar la información del código postal.');
                }
            }
        }
    </script>
</x-app-layout>
