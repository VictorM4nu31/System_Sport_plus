<x-app-layout>
    @section('title', 'Mis Direcciones')

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de éxito/error -->
            @if (session('success'))
                <x-message type="success" dismissible="true" class="mb-4">
                    {{ session('success') }}
                </x-message>
            @endif

            @if (session('error'))
                <x-message type="error" dismissible="true" class="mb-4">
                    {{ session('error') }}
                </x-message>
            @endif

            <!-- Header -->
            <div class="bg-white bg-opacity-90 overflow-hidden shadow-lg sm:rounded-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div>
                        <h2 class="text-display-sm text-primary mb-2">Mis Direcciones</h2>
                        <p class="text-body-md text-primary-light">Administra tus direcciones de envío y facturación</p>
                    </div>
                    <a href="{{ route('usuario.direcciones.crear') }}"
                       class="px-6 py-3 min-h-[44px] bg-primary hover:bg-primary-700 text-white rounded-lg inline-flex items-center justify-center gap-2 transition duration-200 shadow-md w-full sm:w-auto">
                        <span class="material-icons">add</span>
                        <span class="text-body-md font-medium">Nueva Dirección</span>
                    </a>
                </div>
            </div>

            <!-- Lista de direcciones -->
            @if($addresses->count() > 0)
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($addresses as $address)
                        <div class="bg-white bg-opacity-90 rounded-lg shadow-lg p-6 {{ $address->is_default ? 'ring-2 ring-carbon' : '' }}">
                            <!-- Badge de dirección por defecto -->
                            @if($address->is_default)
                                <div class="flex justify-between items-start mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-sm font-medium bg-carbon text-white">
                                        <span class="material-icons text-body-sm mr-1">star</span>
                                        Por defecto
                                    </span>
                                </div>
                            @endif

                            <!-- Información de la dirección -->
                            <div class="space-y-2 mb-4">
                                <h3 class="text-heading-sm text-primary">{{ $address->full_name }}</h3>
                                <p class="text-body-md text-primary-light">
                                    {{ $address->street }} {{ $address->number }}
                                    @if($address->interior_number)
                                        Int. {{ $address->interior_number }}
                                    @endif
                                </p>
                                <p class="text-body-md text-primary-light">
                                    {{ $address->neighborhood }}, {{ $address->municipality }}
                                </p>
                                <p class="text-body-md text-primary-light">
                                    {{ $address->state }}, CP {{ $address->postal_code }}
                                </p>
                                <p class="text-body-md text-primary-light">
                                    <span class="material-icons text-body-sm align-middle">phone</span>
                                    {{ $address->contact_phone }}
                                </p>
                                @if($address->additional_instructions)
                                    <p class="text-body-sm text-gray-500 italic">
                                        {{ $address->additional_instructions }}
                                    </p>
                                @endif
                            </div>

                            <!-- Tipo de dirección -->
                            <div class="mb-4">
                                <span class="inline-flex items-center px-2 py-1 rounded text-body-sm font-medium
                                    {{ $address->address_type === 'shipping' ? 'bg-green-100 text-success-dark' :
                                       ($address->address_type === 'billing' ? 'bg-yellow-100 text-warning-dark' : 'bg-purple-100 text-purple-800') }}">
                                    @if($address->address_type === 'shipping')
                                        <span class="material-icons text-body-sm mr-1">local_shipping</span>
                                        Envío
                                    @elseif($address->address_type === 'billing')
                                        <span class="material-icons text-body-sm mr-1">receipt</span>
                                        Facturación
                                    @else
                                        <span class="material-icons text-body-sm mr-1">home</span>
                                        Ambos
                                    @endif
                                </span>
                            </div>

                            <!-- Acciones -->
                            <div class="flex flex-wrap gap-2">
                                @if(!$address->is_default)
                                    <form method="POST" action="{{ route('usuario.direcciones.predeterminada', $address) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-2 min-h-[36px] bg-[#f4a8ba] hover:bg-[#ed7396] text-primary rounded text-body-sm font-medium transition duration-200">
                                            Hacer predeterminada
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('usuario.direcciones.editar', $address) }}"
                                   class="inline-flex items-center px-3 py-2 min-h-[36px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-body-sm font-medium transition duration-200">
                                    Editar
                                </a>

                                @if($addresses->count() > 1)
                                    <form method="POST" action="{{ route('usuario.direcciones.eliminar', $address) }}"
                                          class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta dirección?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-2 min-h-[36px] bg-red-100 hover:bg-red-200 text-error rounded text-body-sm font-medium transition duration-200">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Estado vacío -->
                <div class="bg-white bg-opacity-90 rounded-lg shadow-lg p-12 text-center">
                    <div class="mb-4">
                        <span class="material-icons text-display-lg text-gray-400">location_off</span>
                    </div>
                    <h3 class="text-heading-md text-primary mb-2">No tienes direcciones guardadas</h3>
                    <p class="text-body-md text-primary-light mb-6">Agrega tu primera dirección para poder realizar pedidos</p>
                    <a href="{{ route('usuario.direcciones.crear') }}"
                       class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-700 text-white rounded-lg transition duration-200 shadow-md">
                        <span class="material-icons mr-2">add</span>
                        <span class="text-body-md font-medium">Agregar Dirección</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
