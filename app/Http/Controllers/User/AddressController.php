<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address; // Asegúrate de importar el modelo Address
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // Método para mostrar el formulario de dirección
    public function create()
    {
        return view('usuario.orders.direccion'); // Asegúrate de tener la vista correcta
    }

    // Método para almacenar la dirección en la base de datos
    public function store(Request $request)
    {
        // Verificar si el usuario ya tiene una dirección
        if (auth()->user()->address()->exists()) {
            return redirect()->back()->with('error', 'Ya tienes una dirección registrada. No puedes añadir otra.');
        }

        // Validación de los datos
        $request->validate([
            'full_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'state' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'number' => 'nullable|string|max:10',
            'interior_number' => 'nullable|string|max:10',
            'contact_phone' => 'nullable|string|max:15',
            'additional_instructions' => 'nullable|string|max:255',
        ]);

        // Guardar la dirección en la base de datos
        Address::create([
            'user_id' => auth()->id(), // Asocia la dirección al usuario actual
            'full_name' => $request->full_name,
            'postal_code' => $request->postal_code,
            'state' => $request->state,
            'municipality' => $request->municipality,
            'neighborhood' => $request->neighborhood,
            'street' => $request->street,
            'number' => $request->number,
            'interior_number' => $request->interior_number,
            'contact_phone' => $request->contact_phone,
            'additional_instructions' => $request->additional_instructions,
        ]);

        // Redirige al usuario con un mensaje de éxito
        return redirect()->route('usuario.dashboard')->with('success', 'Dirección agregada exitosamente.');
    }

    public function index()
    {
        $addresses = Address::all(); // Obtener todas las direcciones
        return view('admin.addresses.index', compact('addresses')); // Asegúrate de tener la vista correcta
    }
}
