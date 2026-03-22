<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials + ['activo' => true], $remember)) {
            $request->session()->regenerate();

            $redirectUrl = $this->redirectByRole();

            // Si el usuario tiene colegio con subdominio, redirigir a su subdominio
            $user = auth()->user();
            if ($user->colegio && $user->colegio->subdominio) {
                $domain = config('app.domain');
                $scheme = $request->isSecure() ? 'https' : 'http';
                $subdomainBase = "{$scheme}://{$user->colegio->subdominio}.{$domain}";

                // Solo redirigir si NO estamos ya en el subdominio correcto
                $currentHost = $request->getHost();
                $expectedHost = "{$user->colegio->subdominio}.{$domain}";
                if (strtolower($currentHost) !== strtolower($expectedHost)) {
                    return redirect()->away($subdomainBase . $redirectUrl);
                }
            }

            return redirect()->intended($redirectUrl);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(): string
    {
        return match (auth()->user()->rol) {
            'superadmin' => '/superadmin/dashboard',
            'admin' => '/admin/dashboard',
            'docente' => '/docente/dashboard',
            'alumno' => '/alumno/dashboard',
            'padre' => '/padre/dashboard',
            default => '/login',
        };
    }
}
