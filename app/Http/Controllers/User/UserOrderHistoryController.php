<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserOrderHistoryController extends Controller
{
    // Mostrar el historial de pedidos del usuario
    public function history()
    {
        // Obtener todos los pedidos del usuario autenticado
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();

        // Pasar los pedidos a la vista
        return view('usuario.orders.history', compact('orders'));
    }

    // Mostrar los detalles de un pedido específico
    public function show($id)
    {
        // Asegurarse de que el pedido pertenece al usuario autenticado
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Pasar el pedido a la vista
        return view('usuario.orders.show', compact('order'));
    }
}
