<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // Mostrar todos los productos
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    // Mostrar formulario para crear un producto
    public function create()
    {
        $categories = Category::all(); // Obtener todas las categorías
        return view('admin.products.create', compact('categories'));
    }

    // Guardar un producto nuevo
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',  // Aquí validamos el campo correcto
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'category_id' => $request->category_id, // Cambiado de category a category_id
            'image' => $this->storeImage($request) // Manejar imagen si se sube
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado con éxito.');
    }

    // Mostrar formulario para editar un producto
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all(); // Obtener todas las categorías
        return view('admin.products.edit', compact('product', 'categories'));
    }


    // Actualizar un producto existente
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',  // Asegúrate de validar el campo category_id
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Si hay una nueva imagen, utilizamos el método storeImage
        if ($request->hasFile('image')) {
            $product->image = $this->storeImage($request);
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'category_id' => $request->category_id, // Cambiado de category a category_id
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado con éxito.');
    }


    // Eliminar un producto
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado.');
    }

    // Método para manejar la subida de imagen
    protected function storeImage(Request $request)
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('products', 'public');
        }
        return null; // Retornar null si no hay imagen
    }
}
