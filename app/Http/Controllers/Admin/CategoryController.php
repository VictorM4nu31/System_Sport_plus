<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    // Mostrar todas las categorías
    public function index()
    {
        Gate::authorize('viewAny', Category::class);

        $categories = Category::withCount('products')->orderBy('name')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    // Mostrar formulario para crear una nueva categoría
    public function create()
    {
        Gate::authorize('create', Category::class);

        return view('admin.categories.create');
    }

    // Guardar una nueva categoría
    public function store(Request $request)
    {
        Gate::authorize('create', Category::class);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada con éxito.');
    }

    // Mostrar formulario para editar una categoría
    public function edit(Category $categoria)
    {
        Gate::authorize('update', $categoria);

        return view('admin.categories.edit', ['category' => $categoria]);
    }

    // Actualizar una categoría
    public function update(Request $request, Category $categoria)
    {
        Gate::authorize('update', $categoria);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$categoria->id,
        ]);

        $categoria->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada con éxito.');
    }

    // Eliminar una categoría
    public function destroy(Category $categoria)
    {
        Gate::authorize('delete', $categoria);

        // No arrastrar el catálogo: la FK es en cascada, bloquear a nivel app.
        if ($categoria->products()->exists()) {
            return redirect()->route('admin.categorias.index')
                ->with('error', "No se puede eliminar «{$categoria->name}» porque tiene productos asociados. Reasígnalos primero.");
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada con éxito.');
    }
}
