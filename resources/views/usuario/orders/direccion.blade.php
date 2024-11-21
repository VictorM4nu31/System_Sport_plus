<x-app-layout>
    <div class="max-w-2xl mx-auto py-6">
        <h1 class="text-2xl font-semibold mb-6">Agrega un domicilio</h1>
        <form method="POST" action="{{ route('usuario.orders.direccion') }}">
            @csrf

            <!-- Nombre y apellido -->
            <div class="mb-4">
                <label for="full_name" class="block text-gray-700">Nombre y apellido</label>
                <input id="full_name" name="full_name" type="text" required class="mt-1 block w-full border-gray-300 p-2 rounded-md" placeholder="Tal cual figure en el INE o IFE">
            </div>

            <!-- Código Postal -->
            <div class="mb-4">
                <label for="postal_code" class="block text-gray-700">Código postal</label>
                <input id="postal_code" name="postal_code" type="text" required class="mt-1 block w-full border-gray-300 p-2 rounded-md" placeholder="Ingresa tu código postal" oninput="fetchAddressData()">
                <a href="https://micodigopostal.org/" class="text-blue-500 text-sm">No sé mi código</a>
            </div>

            <!-- Estado, Municipio/Alcaldía -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="state" class="block text-gray-700">Estado</label>
                    <input id="state" name="state" type="text" readonly class="mt-1 block w-full border-gray-300 p-2 rounded-md bg-gray-100">
                </div>
                <div>
                    <label for="municipality" class="block text-gray-700">Municipio/Alcaldía</label>
                    <input id="municipality" name="municipality" type="text" readonly class="mt-1 block w-full border-gray-300 p-2 rounded-md bg-gray-100">
                </div>
            </div>

            <!-- Colonia -->
            <div class="mb-4">
                <label for="neighborhood" class="block text-gray-700">Colonia</label>
                <input id="neighborhood" name="neighborhood" type="text" required class="mt-1 block w-full border-gray-300 p-2 rounded-md" placeholder="Escribe tu colonia o localidad">
            </div>

        

            <!-- Calle y Número -->
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="col-span-2">
                    <label for="street" class="block text-gray-700">Calle</label>
                    <input id="street" name="street" type="text" required class="mt-1 block w-full border-gray-300 p-2 rounded-md">
                </div>
                <div>
                    <label for="number" class="block text-gray-700">Número</label>
                    <input id="number" name="number" type="text" class="mt-1 block w-full border-gray-300 p-2 rounded-md">
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="no_number" name="no_number" class="mr-2">
                        <label for="no_number" class="text-gray-700 text-sm">Sin número</label>
                    </div>
                </div>
            </div>

            <!-- Número interior/Depto -->
            <div class="mb-4">
                <label for="interior_number" class="block text-gray-700">Nº interior/Depto (opcional)</label>
                <input id="interior_number" name="interior_number" type="text" class="mt-1 block w-full border-gray-300 p-2 rounded-md">
            </div>

            <!-- Teléfono de contacto -->
            <div class="mb-4">
                <label for="contact_phone" class="block text-gray-700">Teléfono de contacto</label>
                <input id="contact_phone" name="contact_phone" type="text" required class="mt-1 block w-full border-gray-300 p-2 rounded-md">
            </div>

            <!-- Indicaciones adicionales -->
            <div class="mb-4">
                <label for="additional_instructions" class="block text-gray-700">Indicaciones adicionales de esta dirección</label>
                <textarea id="additional_instructions" name="additional_instructions" class="mt-1 block w-full border-gray-300 p-2 rounded-md" placeholder="Descripción de la fachada, puntos de referencia, indicaciones de seguridad, etc."></textarea>
            </div>

            <!-- Botón de Guardar -->
            <button type="submit" class="bg-blue-500 text-white p-2 rounded-md w-full">Guardar</button>
        </form>
    </div>

    <script>
        async function fetchAddressData() {
            const postalCode = document.getElementById('postal_code').value;
            if (postalCode.length === 5) {  // Validar que el código postal tenga 5 dígitos
                try {
                    const response = await fetch(`https://api.zippopotam.us/MX/${postalCode}`);
                    if (!response.ok) {
                        throw new Error('Error al obtener los datos de la dirección');
                    }
                    const data = await response.json();
                    
                    // Procesar la respuesta de Zippopotam.us
                    const state = data.places[0]["state"];
                    const municipality = data.places[0]["place name"];

                    document.getElementById('state').value = state;
                    document.getElementById('municipality').value = municipality;

                    // Como Zippopotam no proporciona colonias específicas para México,
                    // podemos usar un valor predeterminado o permitir que el usuario seleccione manualmente.
                    const neighborhoodSelect = document.getElementById('neighborhood');
                    neighborhoodSelect.innerHTML = '<option value="">Selecciona tu colonia</option>';
                    neighborhoodSelect.appendChild(new Option("N/A", "N/A"));
                    
                } catch (error) {
                    console.error(error);
                    alert('No se pudo cargar la información del código postal.');
                }
            }
        }
    </script>
</x-app-layout>
