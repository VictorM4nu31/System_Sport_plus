<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class WorkerController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-workers');

        $workers = User::role('trabajador')->orderBy('name')->paginate(15);

        return view('admin.workers.index', compact('workers'));
    }

    public function create()
    {
        Gate::authorize('manage-workers');

        return view('admin.workers.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-workers');

        // Validar los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Crear el trabajador
        $worker = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Garantizar el rol aunque el seeder aún no se haya ejecutado.
        Role::findOrCreate('trabajador', 'web');
        $worker->assignRole('trabajador');

        // Redirigir de vuelta al índice de trabajadores con un mensaje de éxito
        return redirect()->route('admin.trabajadores.index')->with('success', 'Trabajador creado con éxito.');
    }

    public function edit(User $trabajador)
    {
        Gate::authorize('manage-workers');

        return view('admin.workers.edit', ['worker' => $trabajador]);
    }

    public function update(Request $request, User $trabajador)
    {
        Gate::authorize('manage-workers');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$trabajador->id,
        ]);

        $trabajador->update($request->only('name', 'email'));

        return redirect()->route('admin.trabajadores.index')->with('success', 'Trabajador actualizado con éxito.');
    }

    public function destroy(User $trabajador)
    {
        Gate::authorize('manage-workers');

        $trabajador->delete();

        return redirect()->route('admin.trabajadores.index')->with('success', 'Trabajador eliminado.');
    }
}
