<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Mostrar la lista de deseos
    public function index()
    {
        $wishlist = session()->get('wishlist', []);
        return view('usuario.wishlist.index', compact('wishlist'));
    }

    // Agregar producto a la lista de deseos
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $wishlist = session()->get('wishlist', []);

        if (!isset($wishlist[$id])) {
            $wishlist[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
            ];

            session()->put('wishlist', $wishlist);
            return redirect()->route('usuario.wishlist.index')->with('success', 'Producto agregado a la lista de deseos.');
        }

        return redirect()->route('usuario.wishlist.index')->with('info', 'Este producto ya está en tu lista de deseos.');
    }

    // Eliminar producto de la lista de deseos
    public function remove($id)
    {
        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$id])) {
            unset($wishlist[$id]);
            session()->put('wishlist', $wishlist);
        }

        return redirect()->route('usuario.wishlist.index')->with('success', 'Producto eliminado de la lista de deseos.');
    }
}
