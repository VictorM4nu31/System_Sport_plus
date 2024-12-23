<nav x-data="{ open: false }" class="flex">
    <!-- Menú lateral estático y fijo para pantallas grandes -->
    <aside class="hidden md:block bg-[#282E2E] text-white w-64 p-6 fixed h-full">
        <div class="text-2xl font-semibold mb-8">Campos Sport</div>
        <nav class="space-y-4">
            <!-- Enlace al Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                <span class="material-icons">dashboard</span>
                <span>Dashboard</span>
            </a>

            <!-- Enlaces para Administradores -->
            @if (auth()->user()->hasRole('administrador'))
                <a href="{{ route('admin.workers.index') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">group</span>
                    <span>Ver Trabajadores</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">inventory</span>
                    <span>Gestión de Productos</span>
                </a>
                <a href="{{ route('admin.reports.sales') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">assessment</span>
                    <span>Reportes de Ventas</span>
                </a>
            @endif

            <!-- Enlaces para Trabajadores -->
            @if (auth()->user()->hasRole('trabajador'))
                <a href="{{ route('trabajador.orders.index') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">shopping_cart</span>
                    <span>Pedidos</span>
                </a>
                <a href="{{ route('trabajador.reports.sales') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">bar_chart</span>
                    <span>Reporte de Ventas</span>
                </a>
            @endif

            <!-- Enlaces para Usuarios -->
            @if (auth()->user()->hasRole('usuario'))
                <a href="{{ route('usuario.products.index') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">store</span>
                    <span>Catálogo de Productos</span>
                </a>
                <a href="{{ route('usuario.wishlist.index') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">favorite</span>
                    <span>Lista de Deseos</span>
                </a>
                <a href="{{ route('usuario.orders.direccion') }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-600">
                    <span class="material-icons">location_on</span>
                    <span>Administrar Direcciones</span>
                </a>
            @endif
        </nav>
    </aside>

    <!-- Menú desplegable para pantallas pequeñas -->
    <nav class="bg-[#282E2E] p-4 text-white md:hidden w-full">
        <div class="flex justify-between items-center">
            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div :class="{'block': open, 'hidden': ! open}" class="mt-4 space-y-2" x-show="open">
            <!-- Enlaces de navegación móvil -->
            <a href="{{ route('dashboard') }}" class="block p-2 rounded-md hover:bg-gray-700">
                <span class="material-icons align-middle">dashboard</span>
                <span class="ml-2">Dashboard</span>
            </a>
            @if (auth()->user()->hasRole('administrador'))
                <a href="{{ route('admin.workers.index') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">group</span>
                    <span class="ml-2">Ver Trabajadores</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">inventory</span>
                    <span class="ml-2">Gestión de Productos</span>
                </a>
                <a href="{{ route('admin.reports.sales') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">assessment</span>
                    <span class="ml-2">Reportes de Ventas</span>
                </a>
            @endif
            @if (auth()->user()->hasRole('trabajador'))
                <a href="{{ route('trabajador.orders.index') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">shopping_cart</span>
                    <span class="ml-2">Pedidos</span>
                </a>
                <a href="{{ route('trabajador.reports.sales') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">bar_chart</span>
                    <span class="ml-2">Reporte de Ventas</span>
                </a>
            @endif
            @if (auth()->user()->hasRole('usuario'))
                <a href="{{ route('usuario.products.index') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">store</span>
                    <span class="ml-2">Catálogo de Productos</span>
                </a>
                <a href="{{ route('usuario.wishlist.index') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">favorite</span>
                    <span class="ml-2">Lista de Deseos</span>
                </a>
                <a href="{{ route('usuario.orders.direccion') }}" class="block p-2 rounded-md hover:bg-gray-700">
                    <span class="material-icons align-middle">location_on</span>
                    <span class="ml-2">Administrar Direcciones</span>
                </a>
            @endif
        </div>
    </nav>
</nav>
