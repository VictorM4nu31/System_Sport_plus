<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\AnalyticsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by routes in Laravel 11
    }

    /**
     * Query base con todos los filtros del catálogo.
     * Reutilizada por index (HTML) y search (JSON instantáneo).
     */
    private function baseFilteredQuery(Request $request): Builder
    {
        $query = Product::with(['category', 'reviews'])->where('stock', '>', 0);

        // Filtro por búsqueda de nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por marca
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filtro por tipo de deporte
        if ($request->filled('sport_type')) {
            $query->where('sport_type', $request->sport_type);
        }

        // Filtro por género
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filtro por precio máximo
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtro por productos destacados
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        return $query->orderBy('is_featured', 'desc')->orderBy('name', 'asc');
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);

        // Ordenar por productos destacados primero, luego por nombre
        $products = $this->baseFilteredQuery($request)->paginate(12);

        // Obtener todas las categorías
        $categories = Category::all();

        return view('usuario.products.index', compact('products', 'categories'));
    }

    /**
     * Búsqueda instantánea del catálogo (JSON).
     * Solo campos públicos: nunca expone stripe_product_id / stripe_price_id.
     * La vista usa este endpoint con debounce; sin JS el form GET sigue funcionando.
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:50'],
            'sport_type' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'in:hombre,mujer,unisex'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'featured' => ['nullable', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $started = microtime(true);
        $paginator = $this->baseFilteredQuery($request)->paginate(12);
        $elapsedMs = (int) ((microtime(true) - $started) * 1000);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand,
                'sport_type' => $product->sport_type,
                'price' => (float) $product->price,
                'formatted_price' => '$'.number_format((float) $product->price, 2),
                'stock' => $product->stock,
                'is_featured' => (bool) $product->is_featured,
                'average_rating' => round((float) ($product->average_rating ?? 0), 1),
                'image_url' => $product->image ? asset('storage/products/'.$product->image) : asset('img/logo.png'),
                'category' => $product->category?->name,
                'url' => route('usuario.products.show', $product->id),
            ])->values(),
            'meta' => [
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'elapsed_ms' => $elapsedMs,
            ],
        ]);
    }

    /**
     * Ficha pública del producto para vista rápida y comparador (JSON).
     * Solo campos públicos: nunca expone stripe_product_id / stripe_price_id.
     */
    public function ficha($id): JsonResponse
    {
        $product = Product::with(['category', 'reviews'])->findOrFail($id);
        Gate::authorize('view', $product);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'brand' => $product->brand,
            'model' => $product->model,
            'sport_type' => $product->sport_type,
            'gender' => $product->gender,
            'material' => $product->material,
            'weight' => $product->weight ? (float) $product->weight : null,
            'sizes' => $product->sizes ?? [],
            'colors' => $product->colors ?? [],
            'specifications' => $product->specifications ?? [],
            'description' => $product->description,
            'price' => (float) $product->price,
            'formatted_price' => '$'.number_format((float) $product->price, 2),
            'stock' => $product->stock,
            'is_featured' => (bool) $product->is_featured,
            'average_rating' => round((float) ($product->average_rating ?? 0), 1),
            'reviews_count' => $product->reviews->count(),
            'image_url' => $product->image ? asset('storage/products/'.$product->image) : asset('img/logo.png'),
            'category' => $product->category?->name,
            'url' => route('usuario.products.show', $product->id),
        ]);
    }

    public function show($id, Request $request)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('view', $product);

        // Rastrear vista del producto
        $analyticsService = app(AnalyticsService::class);
        $userId = auth()->check() ? auth()->id() : null;
        $analyticsService->trackProductView($id, $request, $userId);

        // Pasar los datos a la vista
        return view('usuario.products.show', [
            'product' => $product,
            'reviews' => $product->reviews ?? collect(), // Obtener las reseñas del producto
        ]);
    }
}
