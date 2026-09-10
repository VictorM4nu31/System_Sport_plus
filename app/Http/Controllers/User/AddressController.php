<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by routes in Laravel 11
    }

    /**
     * Mostrar todas las direcciones del usuario
     */
    public function index()
    {
        Gate::authorize('viewAny', Address::class);

        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return view('usuario.addresses.index', compact('addresses'));
    }

    /**
     * Mostrar el formulario para crear una nueva dirección
     */
    public function create()
    {
        Gate::authorize('create', Address::class);

        return view('usuario.addresses.create');
    }

    /**
     * Almacenar una nueva dirección
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Address::class);

        $validated = $this->validateAddress($request);

        // Si es la primera dirección del usuario, establecerla como default
        $userAddressCount = Auth::user()->addresses()->count();
        if ($userAddressCount === 0) {
            $validated['is_default'] = true;
        }

        // Si se marca como default, desmarcar las otras direcciones
        if ($request->has('is_default') && $request->is_default) {
            Auth::user()->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $validated['user_id'] = Auth::id();

        $address = Address::create($validated);

        Log::channel('audit')->info('Address created', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'address_id' => $address->id,
            'address_type' => $address->address_type,
            'action' => 'addresses.store',
            'timestamp' => now(),
        ]);

        return redirect()->route('usuario.direcciones.indice')
            ->with('success', 'Dirección agregada exitosamente.');
    }

    /**
     * Mostrar el formulario para editar una dirección
     */
    public function edit(Address $direccion)
    {
        Gate::authorize('update', $direccion);

        return view('usuario.addresses.edit', ['address' => $direccion]);
    }

    /**
     * Actualizar una dirección existente
     */
    public function update(Request $request, Address $direccion)
    {
        Gate::authorize('update', $direccion);

        $oldData = $direccion->toArray();
        $validated = $this->validateAddress($request, $direccion->id);

        // Si se marca como default, desmarcar las otras direcciones
        if ($request->has('is_default') && $request->is_default) {
            Auth::user()->addresses()->where('id', '!=', $direccion->id)
                ->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $direccion->update($validated);

        Log::channel('audit')->info('Address updated', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'address_id' => $direccion->id,
            'old_data' => $oldData,
            'new_data' => $direccion->fresh()->toArray(),
            'action' => 'addresses.update',
            'timestamp' => now(),
        ]);

        return redirect()->route('usuario.direcciones.indice')
            ->with('success', 'Dirección actualizada exitosamente.');
    }

    /**
     * Eliminar una dirección
     */
    public function destroy(Address $direccion)
    {
        Gate::authorize('delete', $direccion);

        $addressData = $direccion->toArray();

        // Si es la dirección por defecto, establecer otra como default
        if ($direccion->is_default) {
            $nextAddress = Auth::user()->addresses()
                ->where('id', '!=', $direccion->id)
                ->first();

            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        $direccion->delete();

        Log::channel('audit')->info('Address deleted', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'address_id' => $direccion->id,
            'address_data' => $addressData,
            'action' => 'addresses.destroy',
            'timestamp' => now(),
        ]);

        return redirect()->route('usuario.direcciones.indice')
            ->with('success', 'Dirección eliminada exitosamente.');
    }

    /**
     * Establecer una dirección como la por defecto
     */
    public function setDefault(Address $direccion)
    {
        Gate::authorize('update', $direccion);

        $direccion->setAsDefault();

        Log::channel('audit')->info('Default address changed', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'address_id' => $direccion->id,
            'action' => 'addresses.setDefault',
            'timestamp' => now(),
        ]);

        return redirect()->route('usuario.direcciones.indice')
            ->with('success', 'Dirección establecida como predeterminada.');
    }

    /**
     * Obtener direcciones del usuario para AJAX
     */
    public function getUserAddresses()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json($addresses);
    }

    /**
     * Validar datos de dirección
     */
    private function validateAddress(Request $request, ?int $addressId = null)
    {
        $rules = ValidationService::addressRules();
        // Override specific rules for this controller
        $rules['number'] = 'nullable|string|max:10';
        $rules['interior_number'] = 'nullable|string|max:10';
        $rules['contact_phone'] = 'required|string|max:15';
        $rules['address_type'] = ['required', Rule::in(['shipping', 'billing', 'both'])];
        $rules['is_default'] = 'boolean';

        return ValidationService::validateRequest($request, $rules);
    }
}
