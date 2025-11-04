<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by routes in Laravel 11
    }
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);

        $query = Product::with(['category', 'reviews'])->where('stock', '>', 0);

        // Filtro por búsqueda de nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
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

        // Ordenar por productos destacados primero, luego por nombre
        $products = $query->orderBy('is_featured', 'desc')
                         ->orderBy('name', 'asc')
                         ->get();

        // Obtener todas las categorías
        $categories = Category::all();

        return view('usuario.products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('view', $product);

        // Pasar los datos a la vista
        return view('usuario.products.show', [
            'product' => $product,
            'reviews' => $product->reviews ?? collect(), // Obtener las reseñas del producto
        ]);
    }
}
