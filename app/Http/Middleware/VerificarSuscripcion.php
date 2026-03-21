<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSuscripcion
{
    /**
     * Verifica que el colegio tenga suscripción vigente (trial o activa).
     * Si la suscripción está vencida, muestra aviso pero permite acceso limitado.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->esSuperAdmin()) {
            return $next($request);
        }

        $colegio = $user->colegio;
        if (!$colegio) {
            return $next($request);
        }

        $suscripcion = $colegio->suscripcionActiva;

        if (!$suscripcion || !$suscripcion->estaVigente()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Suscripción vencida.'], 403);
            }

            return redirect()->back()
                ->with('error', 'La suscripción del colegio ha vencido. Contacte al administrador para renovar.');
        }

        return $next($request);
    }
}
