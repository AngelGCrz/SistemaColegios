<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aviso;
use App\Models\Seccion;
use App\Models\User;
use App\Notifications\AvisoPublicado;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;

class AvisoController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $avisos = Aviso::where('colegio_id', $this->colegioId())
            ->with('autor')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.avisos.index', compact('avisos'));
    }

    public function create()
    {
        $secciones = Seccion::where('colegio_id', $this->colegioId())
            ->with('grado.nivel')
            ->get();

        return view('admin.avisos.create', compact('secciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:200'],
            'contenido' => ['required', 'string', 'max:5000'],
            'destinatario' => ['required', 'in:todos,docentes,alumnos,padres'],
            'seccion_id' => ['nullable', 'exists:secciones,id'],
        ]);

        $aviso = Aviso::create([
            'colegio_id' => $this->colegioId(),
            'user_id' => auth()->id(),
            ...$data,
        ]);

        // Notificar por email a los destinatarios
        $query = User::where('colegio_id', $this->colegioId())->where('activo', true);
        if ($data['destinatario'] !== 'todos') {
            $rolMap = ['docentes' => 'docente', 'alumnos' => 'alumno', 'padres' => 'padre'];
            $query->where('rol', $rolMap[$data['destinatario']]);
        }
        $query->each(fn (User $u) => $u->notify(new AvisoPublicado($aviso)));

        return redirect()->route('admin.avisos.index')
            ->with('success', 'Aviso publicado exitosamente.');
    }

    public function destroy(Aviso $aviso)
    {
        abort_if($aviso->colegio_id !== $this->colegioId(), 403);

        $aviso->delete();

        return back()->with('success', 'Aviso eliminado.');
    }
}
