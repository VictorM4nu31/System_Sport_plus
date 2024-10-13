<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class WorkerController extends Controller
{
    public function create()
    {
        return view('admin.workers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $worker = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Asignar el rol de 'trabajador'
        $worker->assignRole('trabajador');

        return redirect()->route('admin.dashboard')->with('success', 'Trabajador registrado correctamente.');
    }
}

