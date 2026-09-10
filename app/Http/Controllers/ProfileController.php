<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('perfil.editar')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Autocompletar dirección a partir del código postal (API Sepomex).
     *
     * El CP se valida (5 dígitos MX), la petición externa lleva timeouts
     * cortos y cualquier fallo se responde con JSON controlado, nunca 500.
     */
    public function getAddressByPostalCode(Request $request, string $postalCode): JsonResponse
    {
        if (! preg_match('/^\d{5}$/', $postalCode)) {
            return response()->json([
                'message' => 'El código postal debe tener 5 dígitos.',
            ], 422);
        }

        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->retry(2, 200)
                ->get("https://api-sepomex.hckdrk.mx/query/info_cp/{$postalCode}?type=simplified");

            if ($response->failed()) {
                return response()->json([
                    'message' => 'No se pudo consultar el código postal.',
                ], 502);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Servicio de códigos postales no disponible.',
            ], 503);
        }
    }
}
