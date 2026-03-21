<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Padre;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    use FiltraPorColegio;

    public function index(Request $request)
    {
        $query = User::where('colegio_id', $this->colegioId());

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellidos', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        $usuarios = $query->orderBy('apellidos')->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', Rule::in(['admin', 'docente', 'alumno', 'padre'])],
            'telefono' => ['nullable', 'string', 'max:20'],
            'dni' => ['nullable', 'string', 'max:20'],
        ]);

        // Verificar email único dentro del colegio
        $exists = User::where('colegio_id', $this->colegioId())
            ->where('email', $data['email'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'Este email ya está registrado.'])->withInput();
        }

        // Verificar límite de alumnos del plan
        if ($data['rol'] === 'alumno') {
            $colegio = auth()->user()->colegio;
            $suscripcion = $colegio?->suscripcionActiva;
            if ($suscripcion?->plan) {
                $max = $suscripcion->plan->max_alumnos;
                $actual = $colegio->alumnos()->count();
                if ($actual >= $max) {
                    return back()->withErrors(['rol' => "Límite de {$max} alumnos alcanzado. Actualiza tu plan para agregar más."])->withInput();
                }
            }
        }

        DB::transaction(function () use ($data) {
            $user = User::create([
                'colegio_id' => $this->colegioId(),
                'nombre' => $data['nombre'],
                'apellidos' => $data['apellidos'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'rol' => $data['rol'],
                'telefono' => $data['telefono'] ?? null,
                'dni' => $data['dni'] ?? null,
            ]);

            // Crear perfil extendido según rol
            match ($data['rol']) {
                'alumno' => Alumno::create([
                    'colegio_id' => $this->colegioId(),
                    'user_id' => $user->id,
                ]),
                'docente' => Docente::create([
                    'colegio_id' => $this->colegioId(),
                    'user_id' => $user->id,
                ]),
                'padre' => Padre::create([
                    'colegio_id' => $this->colegioId(),
                    'user_id' => $user->id,
                ]),
                default => null,
            };
        });

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $usuario)
    {
        $this->autorizarColegio($usuario);

        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $this->autorizarColegio($usuario);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'dni' => ['nullable', 'string', 'max:20'],
            'activo' => ['boolean'],
        ]);

        // Verificar email único excluyendo al usuario actual
        $exists = User::where('colegio_id', $this->colegioId())
            ->where('email', $data['email'])
            ->where('id', '!=', $usuario->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'Este email ya está registrado.'])->withInput();
        }

        $updateData = collect($data)->except('password')->toArray();
        $updateData['activo'] = $request->boolean('activo');

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $usuario->update($updateData);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $usuario)
    {
        $this->autorizarColegio($usuario);

        // No permitir eliminarse a sí mismo
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    private function autorizarColegio($model): void
    {
        if ($model->colegio_id !== $this->colegioId()) {
            abort(403);
        }
    }
}
