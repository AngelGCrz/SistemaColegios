<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)
            ->where('activo', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'rol' => $user->rol,
                'colegio_id' => $user->colegio_id,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('colegio');

        return response()->json([
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellidos' => $user->apellidos,
            'email' => $user->email,
            'rol' => $user->rol,
            'telefono' => $user->telefono,
            'colegio' => $user->colegio ? [
                'id' => $user->colegio->id,
                'nombre' => $user->colegio->nombre,
            ] : null,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }
}
