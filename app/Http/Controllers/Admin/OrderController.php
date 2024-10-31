<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Mostrar todos los pedidos
    public function index()
    {
        $orders = Order::all();
        return view('admin.orders.index', compact('orders'));
    }

    // Mostrar detalles de un pedido
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // Actualizar el estado de un pedido
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => 'required|string|in:pendiente,en proceso,completado,cancelado',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('admin.orders.index')->with('success', 'Estado del pedido actualizado.');
    }

    // Eliminar un pedido
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pedido eliminado.');
    }

    public function workerIndex()
    {
        $orders = Order::where('status', 'pendiente')->get(); // Solo mostrar pedidos pendientes
        return view('trabajador.orders.index', compact('orders'));
    }

    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'en proceso']); // Actualiza el estado a "en proceso" cuando el trabajador lo acepta
        return redirect()->route('trabajador.orders.index')->with('success', 'Pedido aceptado con éxito.');
    }
}
