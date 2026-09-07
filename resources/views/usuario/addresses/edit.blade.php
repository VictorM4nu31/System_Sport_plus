<x-app-layout>
    @section('title', 'Editar Dirección')

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white bg-opacity-90 overflow-hidden shadow-lg sm:rounded-lg p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('usuario.addresses.index') }}"
                       class="text-gray-600 hover:text-gray-800 transition duration-200">
                        <span class="material-icons">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-display-sm text-primary">Editar Dirección</h2>
                        <p class="text-body-md text-primary-light">Modifica los datos de tu dirección</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white bg-opacity-90 overflow-hidden shadow-lg sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-error px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside text-body-md">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('usuario.addresses.update', $address) }}">
                    @csrf
                    @method('PUT')

                    <!-- Nombre completo -->
                    <div class="mb-6">
                        <label for="full_name" class="block text-body-md font-medium text-primary mb-2">
                            Nombre completo *
                        </label>
                        <input type="text"
                               id="full_name"
                               name="full_name"
                               value="{{ old('full_name', $address->full_name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                               placeholder="Nombre y apellidos completos">
                    </div>

                    <!-- Código postal -->
                    <div class="mb-6">
                        <label for="postal_code" class="block text-body-md font-medium text-primary mb-2">
                            Código postal *
                        </label>
                        <input type="text"
                               id="postal_code"
                               name="postal_code"
                               value="{{ old('postal_code', $address->postal_code) }}"
                               required
                               maxlength="5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                               placeholder="12345"
                               oninput="fetchAddressData()">
                        <p class="mt-1 text-body-sm text-gray-500">
                            <a href="https://micodigopostal.org/" target="_blank" class="text-primary hover:underline">
                                ¿No conoces tu código postal?
                            </a>
                        </p>
                    </div>

                    <!-- Estado y Municipio -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="state" class="block text-body-md font-medium text-primary mb-2">
                                Estado *
                            </label>
                            <input type="text"
                                   id="state"
                                   name="state"
                                   value="{{ old('state', $address->state) }}"
                                   required
                                   readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 focus:outline-none">
                        </div>
                        <div>
                            <label for="municipality" class="block text-body-md font-medium text-primary mb-2">
                                Municipio/Alcaldía *
                            </label>
                            <select id="municipality"
                                    name="municipality"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon">
                                <option value="{{ $address->municipality }}" selected>{{ $address->municipality }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Colonia -->
                    <div class="mb-6">
                        <label for="neighborhood" class="block text-body-md font-medium text-primary mb-2">
                            Colonia *
                        </label>
                        <input type="text"
                               id="neighborhood"
                               name="neighborhood"
                               value="{{ old('neighborhood', $address->neighborhood) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                               placeholder="Nombre de la colonia">
                    </div>

                    <!-- Calle y número -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <label for="street" class="block text-body-md font-medium text-primary mb-2">
                                Calle *
                            </label>
                            <input type="text"
                                   id="street"
                                   name="street"
                                   value="{{ old('street', $address->street) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                                   placeholder="Nombre de la calle">
                        </div>
                        <div>
                            <label for="number" class="block text-body-md font-medium text-primary mb-2">
                                Número
                            </label>
                            <input type="text"
                                   id="number"
                                   name="number"
                                   value="{{ old('number', $address->number) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                                   placeholder="123">
                        </div>
                    </div>

                    <!-- Número interior -->
                    <div class="mb-6">
                        <label for="interior_number" class="block text-body-md font-medium text-primary mb-2">
                            Número interior/Departamento (opcional)
                        </label>
                        <input type="text"
                               id="interior_number"
                               name="interior_number"
                               value="{{ old('interior_number', $address->interior_number) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                               placeholder="Depto 4B, Int. 2, etc.">
                    </div>

                    <!-- Teléfono de contacto -->
                    <div class="mb-6">
                        <label for="contact_phone" class="block text-body-md font-medium text-primary mb-2">
                            Teléfono de contacto *
                        </label>
                        <input type="tel"
                               id="contact_phone"
                               name="contact_phone"
                               value="{{ old('contact_phone', $address->contact_phone) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                               placeholder="55 1234 5678">
                    </div>

                    <!-- Indicaciones adicionales -->
                    <div class="mb-6">
                        <label for="additional_instructions" class="block text-body-md font-medium text-primary mb-2">
                            Indicaciones adicionales
                        </label>
                        <textarea id="additional_instructions"
                                  name="additional_instructions"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-carbon focus:border-carbon"
                                  placeholder="Referencias, descripción de la fachada, etc.">{{ old('additional_instructions', $address->additional_instructions) }}</textarea>
                    </div>

                    <!-- Tipo de dirección -->
                    <div class="mb-6">
                        <label class="block text-body-md font-medium text-primary mb-2">
                            Tipo de dirección *
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio"
                                       name="address_type"
                                       value="shipping"
                                       {{ old('address_type', $address->address_type) === 'shipping' ? 'checked' : '' }}
                                       class="mr-2 text-primary-lighter focus:ring-carbon">
                                <span class="text-body-md text-gray-700">Solo para envíos</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio"
                                       name="address_type"
                                       value="billing"
                                       {{ old('address_type', $address->address_type) === 'billing' ? 'checked' : '' }}
                                       class="mr-2 text-primary-lighter focus:ring-carbon">
                                <span class="text-body-md text-gray-700">Solo para facturación</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio"
                                       name="address_type"
                                       value="both"
                                       {{ old('address_type', $address->address_type) === 'both' ? 'checked' : '' }}
                                       class="mr-2 text-primary-lighter focus:ring-carbon">
                                <span class="text-body-md text-gray-700">Para envíos y facturación</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dirección por defecto -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_default"
                                   value="1"
                                   {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                                   class="mr-2 text-primary-lighter focus:ring-carbon">
                            <span class="text-body-md text-gray-700">Establecer como dirección predeterminada</span>
                        </label>
                    </div>

                    <!-- Botones -->
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="flex-1 bg-primary hover:bg-primary-700 text-white py-3 px-6 rounded-md transition duration-200 shadow-md text-body-md font-medium btn-accessible">
                            Actualizar Dirección
                        </button>
                        <a href="{{ route('usuario.addresses.index') }}"
                           class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-6 rounded-md text-center transition duration-200 text-body-md font-medium">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
                    const currentMunicipality = municipalitySelect.value;
                    municipalitySelect.innerHTML = '<option value="">Selecciona un municipio</option>';

                    municipalities.forEach(municipality => {
                        const option = document.createElement('option');
                        option.value = municipality;
                        option.textContent = municipality;
                        if (municipality === currentMunicipality) {
                            option.selected = true;
                        }
                        municipalitySelect.appendChild(option);
                    });
                } catch (error) {
                    console.error(error);
                    alert('No se pudo cargar la información del código postal. Verifica que sea correcto.');
                }
            }
        }

        // Cargar datos del código postal al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const postalCode = document.getElementById('postal_code').value;
            if (postalCode && postalCode.length === 5) {
                fetchAddressData();
            }
        });
    </script>
</x-app-layout>
