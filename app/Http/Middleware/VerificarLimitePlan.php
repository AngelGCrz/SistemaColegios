<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarLimitePlan
{
    /**
     * Verifica que el colegio no haya excedido el límite de alumnos de su plan.
     * Solo bloquea la creación de nuevos alumnos, no el acceso general.
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
        if (!$suscripcion || !$suscripcion->plan) {
            return $next($request);
        }

        $maxAlumnos = $suscripcion->plan->max_alumnos;
        $alumnosActuales = $colegio->alumnos()->count();

        if ($alumnosActuales >= $maxAlumnos) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => "Límite de alumnos alcanzado ({$alumnosActuales}/{$maxAlumnos}). Actualiza tu plan para agregar más.",
                ], 403);
            }

            return redirect()->back()
                ->with('error', "Has alcanzado el límite de {$maxAlumnos} alumnos de tu plan. Contacta al administrador para actualizar el plan.");
        }

        return $next($request);
    }
}
