<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = User::role('trabajador')->get();
        return view('admin.workers.index', compact('workers'));
    }

    public function create()
    {
        return view('admin.workers.create');
    }

    public function store(Request $request)
    {
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

        // Asignar el rol de trabajador
        $worker->assignRole('trabajador');

        // Redirigir de vuelta al índice de trabajadores con un mensaje de éxito
        return redirect()->route('admin.workers.index')->with('success', 'Trabajador creado con éxito.');
    }


    public function edit($id)
    {
        $worker = User::findOrFail($id);
        return view('admin.workers.edit', compact('worker'));
    }

    public function update(Request $request, $id)
    {
        $worker = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $worker->id,
        ]);

        $worker->update($request->only('name', 'email'));

        return redirect()->route('admin.workers.index')->with('success', 'Trabajador actualizado con éxito.');
    }

    public function suspend($id)
    {
        $worker = User::findOrFail($id);
        $worker->update(['is_active' => false]); // Usaremos un campo 'is_active' para manejar la suspensión.

        return redirect()->route('admin.workers.index')->with('success', 'Trabajador suspendido.');
    }

    public function destroy($id)
    {
        $worker = User::findOrFail($id);
        $worker->delete();

        return redirect()->route('admin.workers.index')->with('success', 'Trabajador eliminado.');
    }
}
