<?php

namespace App\Http\Middleware;

use App\Models\Colegio;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveSubdominio
{
    /**
     * Resuelve el subdominio de la URL y lo asocia al colegio correspondiente.
     * Si hay subdominio, lo guarda en el request para que toda la app lo use.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $appDomain = config('app.domain');

        if (!$appDomain) {
            return $next($request);
        }

        // Extraer subdominio: badenpowell.systemcolegio.com → badenpowell
        $subdomain = $this->extractSubdomain($host, $appDomain);

        if (!$subdomain) {
            return $next($request);
        }

        $colegio = Colegio::where('subdominio', $subdomain)
            ->where('activo', true)
            ->first();

        if (!$colegio) {
            abort(404, 'Colegio no encontrado.');
        }

        // Compartir el colegio resuelto con toda la aplicación
        app()->instance('colegio.subdomain', $colegio);
        view()->share('colegioSubdominio', $colegio);

        return $next($request);
    }

    private function extractSubdomain(string $host, string $domain): ?string
    {
        $domain = strtolower(trim($domain));
        $host = strtolower(trim($host));

        // Si el host es exactamente el dominio principal, no hay subdominio
        if ($host === $domain || $host === 'www.' . $domain) {
            return null;
        }

        // Verificar que termine con el dominio principal
        if (!str_ends_with($host, '.' . $domain)) {
            return null;
        }

        // Extraer la parte del subdominio
        $subdomain = substr($host, 0, strlen($host) - strlen($domain) - 1);

        // Ignorar subdominios del sistema
        if (in_array($subdomain, ['www', 'api', 'mail', 'ftp', 'admin'])) {
            return null;
        }

        return $subdomain ?: null;
    }
}
