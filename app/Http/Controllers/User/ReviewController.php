<?php

namespace App\Http\Controllers\User;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Almacenar una nueva reseña.
     */
    public function store(Request $request, Product $producto)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        // Obtener el usuario desde la request
        $user = $request->user();

        // Solo quien compró (pedido pagado con este producto) puede reseñar.
        $hasPurchased = $user->orders()
            ->where('payment_status', PaymentStatus::PAID->value)
            ->whereHas('orderItems', fn ($items) => $items->where('product_id', $producto->id))
            ->exists();

        if (! $hasPurchased) {
            return redirect()->route('usuario.productos.ver', $producto->id)
                ->with('error', 'Solo puedes reseñar productos que hayas comprado.');
        }

        // Una sola reseña por usuario y producto.
        if (Review::where('user_id', $user->id)->where('product_id', $producto->id)->exists()) {
            return redirect()->route('usuario.productos.ver', $producto->id)
                ->with('error', 'Ya publicaste una reseña para este producto.');
        }

        Review::create([
            'user_id' => $user->id, // Usuario desde la request
            'product_id' => $producto->id,
            'rating' => $request->input('rating'),
            'review' => $request->input('review'),
        ]);

        return redirect()->route('usuario.productos.ver', $producto->id)
            ->with('success', 'Reseña añadida con éxito.');
    }

    /**
     * Mostrar las reseñas de un producto.
     */
    public function index(Product $producto)
    {
        // Cargar el producto y sus reseñas
        $reviews = $producto->reviews()->with('user')->latest()->paginate(10);

        // Renderizar la vista de reseñas
        return view('usuario.reviews.index', ['product' => $producto, 'reviews' => $reviews]);
    }

    /**
     * Mostrar la página de detalles del producto con sus reseñas.
     */
    public function show(Product $producto)
    {
        // Calcular el promedio de calificaciones
        $averageRating = round($producto->reviews()->avg('rating'), 1); // Promedio redondeado a un decimal

        // Obtener las reseñas del producto con los usuarios relacionados
        $reviews = $producto->reviews()->with('user')->latest()->paginate(10);

        // Renderizar la vista de detalles del producto
        return view('usuario.products.show', ['product' => $producto, 'averageRating' => $averageRating, 'reviews' => $reviews]);
    }
}
