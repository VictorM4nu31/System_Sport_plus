<?php

namespace App\Http\Controllers\User;

use App\Contracts\StockManagementInterface;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(protected StockManagementInterface $stockService) {}

    // Mostrar la lista de deseos con datos vivos de BD (precio/imagen/stock reales)
    public function index()
    {
        $wishlist = $this->rebuildFromDatabase(session()->get('wishlist', []));

        return view('usuario.wishlist.index', compact('wishlist'));
    }

    /**
     * Reconstruye la wishlist con precios e imágenes actuales.
     * Devuelve [id => ['name','price','saved_price','image','stock','exists']].
     */
    private function rebuildFromDatabase(array $wishlist): array
    {
        if (empty($wishlist)) {
            return [];
        }

        $products = Product::whereIn('id', array_keys($wishlist))->get()->keyBy('id');
        $rebuilt = [];

        foreach ($wishlist as $id => $details) {
            $product = $products->get($id);

            if (! $product) {
                continue;
            }

            $rebuilt[$id] = [
                'name' => $product->name,
                'price' => (float) $product->price,
                'saved_price' => isset($details['price']) ? (float) $details['price'] : (float) $product->price,
                'image' => $product->image,
                'stock' => $product->stock,
                'brand' => $product->brand,
            ];
        }

        return $rebuilt;
    }

    // Agregar producto a la lista de deseos
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $wishlist = session()->get('wishlist', []);

        if (! isset($wishlist[$id])) {
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

    // Mover producto de la lista de deseos al carrito (valida stock disponible)
    public function moveToCart($id)
    {
        $product = Product::findOrFail($id);
        $availableStock = $this->stockService->getAvailableStock($id);

        $cart = session()->get('cart', []);
        $currentQuantity = $cart[$id]['quantity'] ?? 0;

        if ($currentQuantity + 1 > $availableStock) {
            return redirect()->route('usuario.wishlist.index')
                ->with('error', "Stock insuficiente para {$product->name}. Disponible: {$availableStock}");
        }

        $cart[$id] = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $currentQuantity + 1,
        ];
        session()->put('cart', $cart);

        $wishlist = session()->get('wishlist', []);
        unset($wishlist[$id]);
        session()->put('wishlist', $wishlist);

        return redirect()->route('usuario.cart.index')
            ->with('success', "{$product->name} movido al carrito.");
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
