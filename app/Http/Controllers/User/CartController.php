<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('usuario.cart.index', compact('cart'));
    }

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


    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('usuario.cart.index')->with('success', 'Producto eliminado del carrito.');
    }
}
