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
        return redirect()->route('admin.workers.index')->with('success', 'Trabajador creado con éxito.');
    }

    public function edit($id)
    {
        Gate::authorize('manage-workers');

        // Solo trabajadores: nunca editar administradores u otros roles.
        $worker = User::role('trabajador')->findOrFail($id);

        return view('admin.workers.edit', compact('worker'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('manage-workers');

        $worker = User::role('trabajador')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$worker->id,
        ]);

        $worker->update($request->only('name', 'email'));

        return redirect()->route('admin.workers.index')->with('success', 'Trabajador actualizado con éxito.');
    }

    public function destroy($id)
    {
        Gate::authorize('manage-workers');

        $worker = User::role('trabajador')->findOrFail($id);
        $worker->delete();

        return redirect()->route('admin.workers.index')->with('success', 'Trabajador eliminado.');
    }
}
