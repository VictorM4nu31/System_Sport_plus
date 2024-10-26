<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Mostrar el carrito de compras
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('usuario.cart.index', compact('cart'));
    }

    // Agregar productos al carrito
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);  // Obtener la cantidad seleccionada

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;  // Incrementar la cantidad si ya está en el carrito
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('usuario.products.index')->with('success', 'Producto agregado al carrito.');
    }

    // Eliminar productos del carrito
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('usuario.cart.index')->with('success', 'Producto eliminado del carrito.');
    }

    // Procesar el pedido después del pago exitoso
    public function processOrder(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('usuario.cart.index')->with('error', 'No tienes productos en el carrito.');
        }

        // Crear el pedido
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $request->total, // Total enviado desde PayPal
            'status' => 'pendiente', // Estado inicial del pedido
            'payment_status' => 'pagado',
            'shipping_address' => $request->shipping_address ?? 'No definida', // Dirección de envío si es proporcionada
        ]);

        // Crear los elementos del pedido
        foreach ($cart as $id => $details) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $details['quantity'],
                'price' => $details['price'],
            ]);
        }

        // Limpiar el carrito después del pago
        session()->forget('cart');

        // Redirigir al historial de pedidos
        return redirect()->route('usuario.orders.history')->with('success', '¡Pedido procesado con éxito!');
    }

}
