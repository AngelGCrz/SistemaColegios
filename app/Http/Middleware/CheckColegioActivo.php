<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckColegioActivo
{
    /**
     * Verifica que el colegio del usuario esté activo y con suscripción vigente.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super-admin no tiene colegio vinculado
        if ($user->esSuperAdmin()) {
            return $next($request);
        }

        $colegio = $user->colegio;

        if (!$colegio || !$colegio->estaActivo()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'La suscripción del colegio ha expirado. Contacte al administrador.');
        }

        return $next($request);
    }
}
