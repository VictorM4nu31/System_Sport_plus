<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && $user->hasRole('administrador')) {
            return redirect()->route('admin.panel');
        } elseif ($user && $user->hasRole('trabajador')) {
            return redirect()->route('trabajador.panel');
        } elseif ($user && $user->hasRole('usuario')) {
            return redirect()->route('usuario.panel');
        }

        return redirect()->route('welcome');
    }
}
