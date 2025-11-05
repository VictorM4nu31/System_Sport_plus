<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && $user->hasRole('administrador')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user && $user->hasRole('trabajador')) {
            return redirect()->route('trabajador.orders.index');
        } elseif ($user && $user->hasRole('usuario')) {
            return redirect()->route('usuario.dashboard');
        }

        return redirect()->route('welcome');
    }
}
