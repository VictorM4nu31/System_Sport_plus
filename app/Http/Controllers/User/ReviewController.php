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
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        // El producto debe existir: evita el 500 por FK ante IDs inexistentes.
        $product = Product::findOrFail($productId);

        // Obtener el usuario desde la request
        $user = $request->user();

        // Solo quien compró (pedido pagado con este producto) puede reseñar.
        $hasPurchased = $user->orders()
            ->where('payment_status', PaymentStatus::PAID->value)
            ->whereHas('orderItems', fn ($items) => $items->where('product_id', $product->id))
            ->exists();

        if (! $hasPurchased) {
            return redirect()->route('usuario.products.show', $product->id)
                ->with('error', 'Solo puedes reseñar productos que hayas comprado.');
        }

        // Una sola reseña por usuario y producto.
        if (Review::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
            return redirect()->route('usuario.products.show', $product->id)
                ->with('error', 'Ya publicaste una reseña para este producto.');
        }

        Review::create([
            'user_id' => $user->id, // Usuario desde la request
            'product_id' => $product->id,
            'rating' => $request->input('rating'),
            'review' => $request->input('review'),
        ]);

        return redirect()->route('usuario.products.show', $product->id)
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
