<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campos Sport</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Header -->
<header class="bg-[#282E2E] text-white py-4">
    <div class="container mx-auto flex justify-between items-center px-6">
        <!-- Logo or Title -->
        <h1 class="text-2xl font-bold">Campos Sport</h1>

        <!-- Main Navigation -->
        <nav class="flex space-x-6">
            <a href="#" class="hover:text-red-500">Mujer</a>
            <a href="#" class="hover:text-red-500">Hombre</a>
            <a href="#" class="hover:text-red-500">Niños</a>
            <a href="#" class="hover:text-red-500">Accesorios</a>
        </nav>

        <!-- Authentication Links -->
        @if (Route::has('login'))
        <div class="flex items-center space-x-4">
            @auth
            <a href="{{ url('/dashboard') }}" class="rounded-md px-3 py-2 text-white hover:text-red-500 transition">
                Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-white hover:text-red-500 transition">
                Iniciar sesión
            </a>

            @if (Route::has('register'))
            <a href="{{ route('register') }}" class="rounded-md px-3 py-2 text-white hover:text-red-500 transition">
                Regístrese
            </a>
            @endif
            @endauth
        </div>
        @endif
    </div>
</header>



    <!-- Carrusel -->
<div class="relative bg-black overflow-hidden">
    <div class="container mx-auto relative">
        <!-- Slider -->
        <div id="carousel" class="flex transition-transform duration-700 ease-in-out">
            <img src="../img/banner1.jpg" alt="Slide 1" class="w-full object-cover">
            <img src="../img/banner2.jpg" alt="Slide 2" class="w-full object-cover">
            <img src="../img/banner3.jpg" alt="Slide 3" class="w-full object-cover">
        </div>

        <!-- Navigation -->
        <div class="absolute inset-0 flex items-center justify-between px-6">
            <button id="prev" class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">
                &#x276E;
            </button>
            <button id="next" class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">
                &#x276F;
            </button>
        </div>

        <!-- Indicators -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <button class="w-3 h-3 bg-white rounded-full hover:bg-gray-300" data-slide="0"></button>
            <button class="w-3 h-3 bg-white rounded-full hover:bg-gray-300" data-slide="1"></button>
            <button class="w-3 h-3 bg-white rounded-full hover:bg-gray-300" data-slide="2"></button>
        </div>
    </div>
</div>

<script>
    const carousel = document.getElementById('carousel');
    const slides = carousel.children;
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');
    const indicators = document.querySelectorAll('[data-slide]');
    let currentIndex = 0;

    // Function to update the carousel position
    const updateCarousel = (index) => {
        carousel.style.transform = `translateX(-${index * 100}%)`;
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('bg-gray-300', i === index);
        });
    };

    // Automatic sliding
    const autoSlide = () => {
        currentIndex = (currentIndex + 1) % slides.length;
        updateCarousel(currentIndex);
    };

    let autoSlideInterval = setInterval(autoSlide, 5000); // Slide every 5 seconds

    // Button events
    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        updateCarousel(currentIndex);
        resetAutoSlide();
    });

    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % slides.length;
        updateCarousel(currentIndex);
        resetAutoSlide();
    });

    // Indicator events
    indicators.forEach((indicator, i) => {
        indicator.addEventListener('click', () => {
            currentIndex = i;
            updateCarousel(currentIndex);
            resetAutoSlide();
        });
    });

    // Reset auto-slide interval
    const resetAutoSlide = () => {
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(autoSlide, 5000);
    };
</script>


    <!-- Featured Sections -->
    <section class="py-12">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-8">Ofertas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="relative group">
                    <img src="https://via.placeholder.com/400x300?text=Yoga+Gear" alt="Yoga Gear" class="rounded-lg shadow-lg object-cover w-full h-60">
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-lg font-bold">
                        Shop Yoga Gear
                    </div>
                </div>
                <div class="relative group">
                    <img src="https://via.placeholder.com/400x300?text=Running+Wear" alt="Running Wear" class="rounded-lg shadow-lg object-cover w-full h-60">
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-lg font-bold">
                        Shop Running Wear
                    </div>
                </div>
                <div class="relative group">
                    <img src="https://via.placeholder.com/400x300?text=Accessories" alt="Accessories" class="rounded-lg shadow-lg object-cover w-full h-60">
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-lg font-bold">
                        Shop Accessories
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-8">Lo más vendido</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <img src="https://via.placeholder.com/300x200?text=Product+1" alt="Product 1" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold">Product Name</h3>
                        <p class="text-gray-600">$49.99</p>
                        <button class="mt-2 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Add to Cart</button>
                    </div>
                </div>
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <img src="https://via.placeholder.com/300x200?text=Product+2" alt="Product 2" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold">Product Name</h3>
                        <p class="text-gray-600">$59.99</p>
                        <button class="mt-2 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Add to Cart</button>
                    </div>
                </div>
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <img src="https://via.placeholder.com/300x200?text=Product+3" alt="Product 3" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold">Product Name</h3>
                        <p class="text-gray-600">$69.99</p>
                        <button class="mt-2 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Add to Cart</button>
                    </div>
                </div>
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <img src="https://via.placeholder.com/300x200?text=Product+4" alt="Product 4" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold">Product Name</h3>
                        <p class="text-gray-600">$79.99</p>
                        <button class="mt-2 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-8">Marcas</h2>
        <div class="flex flex-wrap justify-center items-center gap-6">
            <!-- Brand Item -->
            <div class="flex flex-col items-center space-y-2">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex justify-center items-center overflow-hidden">
                    <img src="https://via.placeholder.com/100x100?text=Nike" alt="Nike" class="object-contain w-16 h-16">
                </div>
                <span class="text-sm font-semibold text-black">Nike</span>
            </div>
            <!-- Brand Item -->
            <div class="flex flex-col items-center space-y-2">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex justify-center items-center overflow-hidden">
                    <img src="https://via.placeholder.com/100x100?text=Adidas" alt="Adidas" class="object-contain w-16 h-16">
                </div>
                <span class="text-sm font-semibold text-black">Adidas</span>
            </div>
            <!-- Brand Item -->
            <div class="flex flex-col items-center space-y-2">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex justify-center items-center overflow-hidden">
                    <img src="https://via.placeholder.com/100x100?text=Puma" alt="Puma" class="object-contain w-16 h-16">
                </div>
                <span class="text-sm font-semibold text-black">Puma</span>
            </div>
            <!-- Brand Item -->
            <div class="flex flex-col items-center space-y-2">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex justify-center items-center overflow-hidden">
                    <img src="https://via.placeholder.com/100x100?text=Under+Armour" alt="Under Armour" class="object-contain w-16 h-16">
                </div>
                <span class="text-sm font-semibold text-black">Under Armour</span>
            </div>
            <!-- Brand Item -->
            <div class="flex flex-col items-center space-y-2">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex justify-center items-center overflow-hidden">
                    <img src="https://via.placeholder.com/100x100?text=Jordan" alt="Jordan" class="object-contain w-16 h-16">
                </div>
                <span class="text-sm font-semibold text-black">Jordan</span>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-[#282E2E] text-white py-8">
    <div class="container mx-auto px-6 text-center">
        <p>&copy; 2024 Sports Store. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>
