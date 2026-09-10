<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\StripeProductService;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by routes in Laravel 11
    }

    // Mostrar todos los productos con filtros
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);

        $query = Product::with('category');

        // Filtro por búsqueda de nombre (insensible a mayúsculas en cualquier motor)
        if ($request->filled('search')) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($request->search).'%']);
        }

        // Filtro por marca
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filtro por tipo de deporte
        if ($request->filled('sport_type')) {
            $query->where('sport_type', $request->sport_type);
        }

        // Filtro por género
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Ordenar por productos destacados primero, luego por nombre
        $products = $query->orderBy('is_featured', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        Log::channel('audit')->info('Admin viewed products list', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'action' => 'products.index',
            'filters' => $request->only(['search', 'brand', 'sport_type', 'gender', 'category_id']),
            'timestamp' => now(),
        ]);

        return view('admin.products.index', compact('products'));
    }

    // Mostrar formulario para crear un producto
    public function create()
    {
        Gate::authorize('create', Product::class);

        $categories = Category::all(); // Obtener todas las categorías

        return view('admin.products.create', compact('categories'));
    }

    // Guardar un producto nuevo
    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);

        ValidationService::validateRequest($request, ValidationService::productRules());

        // Procesar especificaciones técnicas
        $specifications = $this->processSpecifications($request);

        $product = Product::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image' => $this->storeImage($request),
            'sizes' => $request->sizes ?? [],
            'colors' => $request->colors ?? [],
            'material' => $request->material,
            'gender' => $request->gender,
            'sport_type' => $request->sport_type,
            'weight' => $request->weight,
            'specifications' => $specifications,
            'is_featured' => $request->has('is_featured'),
            'sku' => $request->sku,
        ]);

        Log::channel('audit')->info('Product created', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'action' => 'products.store',
            'timestamp' => now(),
        ]);

        $this->syncProductWithStripe($product);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado con éxito.');
    }

    // Mostrar formulario para editar un producto
    public function edit(Product $producto)
    {
        Gate::authorize('update', $producto);

        $categories = Category::all(); // Obtener todas las categorías

        return view('admin.products.edit', ['product' => $producto, 'categories' => $categories]);
    }

    // Actualizar un producto existente
    public function update(Request $request, Product $producto)
    {
        Gate::authorize('update', $producto);

        ValidationService::validateRequest($request, ValidationService::productRules($producto->id));

        $oldData = $producto->toArray();

        // Procesar especificaciones técnicas
        $specifications = $this->processSpecifications($request);

        // Si hay una nueva imagen, eliminar la anterior y guardar la nueva
        if ($request->hasFile('image')) {
            $this->deleteOldImage($producto->image);
            $producto->image = $this->storeImage($request);
        }

        $producto->update([
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'sizes' => $request->sizes ?? [],
            'colors' => $request->colors ?? [],
            'material' => $request->material,
            'gender' => $request->gender,
            'sport_type' => $request->sport_type,
            'weight' => $request->weight,
            'specifications' => $specifications,
            'is_featured' => $request->has('is_featured'),
            'sku' => $request->sku,
        ]);

        Log::channel('audit')->info('Product updated', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'product_id' => $producto->id,
            'product_name' => $producto->name,
            'old_data' => $oldData,
            'new_data' => $producto->fresh()->toArray(),
            'action' => 'productos.update',
            'timestamp' => now(),
        ]);

        $this->syncProductWithStripe($producto);

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado con éxito.');
    }

    // Eliminar un producto
    public function destroy(Product $producto)
    {
        Gate::authorize('delete', $producto);

        // Preservar el historial: no borrar productos con items en pedidos.
        if ($producto->orderItems()->exists()) {
            return redirect()->route('admin.productos.index')
                ->with('error', "No se puede eliminar «{$producto->name}» porque aparece en pedidos. Considéralo descontinuado en su lugar.");
        }

        $productData = $producto->toArray();

        $this->archiveProductOnStripe($producto);

        $producto->delete();

        Log::channel('audit')->info('Product deleted', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'product_id' => $producto->id,
            'product_data' => $productData,
            'action' => 'productos.destroy',
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }

    // Método para manejar la subida de imagen
    protected function storeImage(Request $request)
    {
        if ($request->hasFile('image')) {
            // Generar nombre único para evitar conflictos
            $fileName = time().'_'.$request->file('image')->getClientOriginalName();

            // Optimizar imagen antes de guardar (opcional)
            $path = $request->file('image')->storeAs('products', $fileName, 'public');

            return $fileName;
        }

        return null;
    }

    // Método para eliminar imagen anterior
    protected function deleteOldImage($imagePath)
    {
        if ($imagePath && Storage::disk('public')->exists('products/'.$imagePath)) {
            Storage::disk('public')->delete('products/'.$imagePath);
        }
    }

    // Método para procesar especificaciones técnicas
    protected function processSpecifications(Request $request)
    {
        $specifications = [];

        if ($request->has('spec_keys') && $request->has('spec_values')) {
            $keys = $request->spec_keys;
            $values = $request->spec_values;

            for ($i = 0; $i < count($keys); $i++) {
                if (! empty($keys[$i]) && ! empty($values[$i])) {
                    $specifications[$keys[$i]] = $values[$i];
                }
            }
        }

        return $specifications;
    }

    // Mostrar detalles de un producto
    public function show(Product $producto)
    {
        $producto->load(['category', 'reviews']);
        Gate::authorize('view', $producto);

        return view('admin.products.show', ['product' => $producto]);
    }

    // Mostrar productos en la vista de welcome
    public function welcome()
    {
        $products = Product::all();

        return view('welcome', compact('products'));
    }

    // Sincronizar producto con Stripe manualmente
    public function syncWithStripe(Product $producto)
    {
        Gate::authorize('update', $producto);

        $stripeService = new StripeProductService;
        $result = $stripeService->syncWithStripe($producto);

        if ($result['success']) {
            Log::channel('audit')->info('Product synced with Stripe manually', [
                'user_id' => Auth::id(),
                'product_id' => $producto->id,
                'stripe_product_id' => $producto->fresh()->stripe_product_id,
                'action' => 'productos.sync_stripe',
                'timestamp' => now(),
            ]);

            return redirect()->back()->with('success', 'Producto sincronizado con Stripe exitosamente.');
        } else {
            return redirect()->back()->with('error', 'Error al sincronizar con Stripe: '.$result['error']);
        }
    }

    // Ver información de Stripe del producto
    public function stripeInfo(Product $producto)
    {
        Gate::authorize('view', $producto);

        $stripeService = new StripeProductService;
        $stripeInfo = $stripeService->getStripeProduct($producto);

        return response()->json($stripeInfo);
    }

    /**
     * Sincronizar el producto con Stripe sin que un fallo de la API rompa el
     * guardado local del producto.
     */
    private function syncProductWithStripe(Product $product): void
    {
        if (! config('stripe.auto_sync', true)) {
            return;
        }

        try {
            if ($product->stripe_product_id) {
                (new StripeProductService)->updateStripeProduct($product);
            } else {
                (new StripeProductService)->createStripeProduct($product);
            }
        } catch (\Throwable $e) {
            Log::channel('audit')->error('Stripe product sync failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'action' => 'products.sync_stripe',
            ]);
        }
    }

    /**
     * Archivar el producto en Stripe antes de eliminarlo localmente.
     */
    private function archiveProductOnStripe(Product $product): void
    {
        if (! config('stripe.auto_sync', true) || ! $product->stripe_product_id) {
            return;
        }

        try {
            (new StripeProductService)->deleteStripeProduct($product);
        } catch (\Throwable $e) {
            Log::channel('audit')->error('Stripe product archive failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'action' => 'products.archive_stripe',
            ]);
        }
    }
}
