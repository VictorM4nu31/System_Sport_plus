<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Services\ValidationService;
use App\Services\StripeProductService;
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

        // Filtro por búsqueda de nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
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
                         ->get();

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

        return redirect()->route('admin.products.index')->with('success', 'Producto creado con éxito.');
    }

    // Mostrar formulario para editar un producto
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('update', $product);

        $categories = Category::all(); // Obtener todas las categorías
        return view('admin.products.edit', compact('product', 'categories'));
    }


    // Actualizar un producto existente
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('update', $product);

        ValidationService::validateRequest($request, ValidationService::productRules($id));

        $oldData = $product->toArray();

        // Procesar especificaciones técnicas
        $specifications = $this->processSpecifications($request);

        // Si hay una nueva imagen, eliminar la anterior y guardar la nueva
        if ($request->hasFile('image')) {
            $this->deleteOldImage($product->image);
            $product->image = $this->storeImage($request);
        }

        $product->update([
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
            'product_id' => $product->id,
            'product_name' => $product->name,
            'old_data' => $oldData,
            'new_data' => $product->fresh()->toArray(),
            'action' => 'products.update',
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado con éxito.');
    }


    // Eliminar un producto
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('delete', $product);

        $productData = $product->toArray();
        $product->delete();

        Log::channel('audit')->info('Product deleted', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'product_id' => $id,
            'product_data' => $productData,
            'action' => 'products.destroy',
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado.');
    }

    // Método para manejar la subida de imagen
    protected function storeImage(Request $request)
    {
        if ($request->hasFile('image')) {
            // Generar nombre único para evitar conflictos
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();

            // Optimizar imagen antes de guardar (opcional)
            $path = $request->file('image')->storeAs('products', $fileName, 'public');

            return $fileName;
        }
        return null;
    }

    // Método para eliminar imagen anterior
    protected function deleteOldImage($imagePath)
    {
        if ($imagePath && Storage::disk('public')->exists('products/' . $imagePath)) {
            Storage::disk('public')->delete('products/' . $imagePath);
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
                if (!empty($keys[$i]) && !empty($values[$i])) {
                    $specifications[$keys[$i]] = $values[$i];
                }
            }
        }

        return $specifications;
    }
    // Mostrar detalles de un producto
    public function show($id)
    {
        $product = Product::with(['category', 'reviews'])->findOrFail($id);
        Gate::authorize('view', $product);

        return view('admin.products.show', compact('product'));
    }
    // Mostrar productos en la vista de welcome
    public function welcome()
    {
        $products = Product::all();
        return view('welcome', compact('products'));
    }

    // Sincronizar producto con Stripe manualmente
    public function syncWithStripe($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('update', $product);

        $stripeService = new StripeProductService();
        $result = $stripeService->syncWithStripe($product);

        if ($result['success']) {
            Log::channel('audit')->info('Product synced with Stripe manually', [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'stripe_product_id' => $product->fresh()->stripe_product_id,
                'action' => 'products.sync_stripe',
                'timestamp' => now(),
            ]);

            return redirect()->back()->with('success', 'Producto sincronizado con Stripe exitosamente.');
        } else {
            return redirect()->back()->with('error', 'Error al sincronizar con Stripe: ' . $result['error']);
        }
    }

    // Ver información de Stripe del producto
    public function stripeInfo($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('view', $product);

        $stripeService = new StripeProductService();
        $stripeInfo = $stripeService->getStripeProduct($product);

        return response()->json($stripeInfo);
    }

}


