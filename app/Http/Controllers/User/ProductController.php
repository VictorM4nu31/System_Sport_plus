<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtrar por categoría si se selecciona
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrar por precio mínimo
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filtrar por precio máximo
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Obtener los productos filtrados
        $products = $query->paginate(10);  // Mostrar 10 productos por página

        // Obtener todas las categorías
        $categories = Category::all();

        return view('usuario.products.index', compact('products', 'categories'));
    }
}
