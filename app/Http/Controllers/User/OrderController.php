<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (count($cart) == 0) {
            return response()->json(['error' => 'El carrito está vacío.'], 400);
        }

        // Crear el pedido
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $request->total,
            'status' => 'pendiente',
            'payment_status' => 'pagado',
        ]);

        // Guardar los productos en order_items
        foreach ($cart as $productId => $details) {
            $product = Product::findOrFail($productId);

            // Verificar si hay suficiente stock
            if ($product->stock < $details['quantity']) {
                return response()->json(['error' => 'No hay suficiente stock para el producto: ' . $product->name], 400);
            }

            // Reducir el stock del producto
            $product->stock -= $details['quantity'];
            $product->save();

            // Crear los elementos del pedido
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $details['quantity'],
                'price' => $details['price'],
            ]);
        }

        // Vaciar el carrito
        session()->forget('cart');

        return response()->json(['success' => 'Pedido creado con éxito.'], 200);
    }

    // Mostrar los pedidos del usuario
    public function index()
    {
        $orders = Auth::user()->orders; // Obtener los pedidos del usuario autenticado
        return view('usuario.orders.index', compact('orders'));
    }

    // Mostrar los detalles de un pedido específico
    public function show($id)
    {
        $order = Order::findOrFail($id);

        // Verificar que el pedido pertenece al usuario
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('usuario.orders.index')->with('error', 'No tienes acceso a este pedido.');
        }

        return view('usuario.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Validar el estado del pedido
        $request->validate([
            'status' => 'required|string|in:pendiente,en proceso,completado,cancelado',
        ]);

        // Encontrar el pedido y actualizar su estado
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Estado del pedido actualizado con éxito.');
    }
}
