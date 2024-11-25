<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Almacenar una nueva reseña.
     */
    public function store(Request $request, $productId)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'required|string|max:1000',
    ]);

    // Obtener el usuario desde la request
    $user = $request->user();

    Review::create([
        'user_id' => $user->id, // Usuario desde la request
        'product_id' => $productId,
        'rating' => $request->input('rating'),
        'review' => $request->input('review'),
    ]);

    return redirect()->route('usuario.products.show', $productId)
                     ->with('success', 'Reseña añadida con éxito.');
}

    /**
     * Mostrar las reseñas de un producto.
     */
    public function index($productId)
    {
        // Cargar el producto y sus reseñas
        $product = Product::findOrFail($productId);
        $reviews = $product->reviews()->with('user')->latest()->paginate(10);

        // Renderizar la vista de reseñas
        return view('usuario.reviews.index', compact('product', 'reviews'));
    }

    /**
     * Mostrar la página de detalles del producto con sus reseñas.
     */
    public function show($id)
    {
        // Cargar el producto
        $product = Product::findOrFail($id);

        // Calcular el promedio de calificaciones
        $averageRating = round($product->reviews()->avg('rating'), 1); // Promedio redondeado a un decimal

        // Obtener las reseñas del producto con los usuarios relacionados
        $reviews = $product->reviews()->with('user')->latest()->paginate(10);

        // Renderizar la vista de detalles del producto
        return view('usuario.products.show', compact('product', 'averageRating', 'reviews'));
    }
}
