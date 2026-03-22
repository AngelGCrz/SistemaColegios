<?php

namespace App\Http\Controllers;

use App\Models\RecursoDigital;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BibliotecaPublicaController extends Controller
{
    use FiltraPorColegio;

    public function index(Request $request)
    {
        $colegioId = $this->colegioId();

        $query = RecursoDigital::where('publico', true)
            ->with('user:id,nombre,apellidos')
            ->when($colegioId, fn ($q) => $q->where('colegio_id', $colegioId))
            ->when($request->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($request->materia, fn ($q, $v) => $q->where('materia', $v))
            ->when($request->buscar, fn ($q, $v) => $q->where('titulo', 'like', "%{$v}%"))
            ->orderByDesc('created_at');

        $recursos = $query->paginate(12);

        $materias = RecursoDigital::where('publico', true)
            ->when($colegioId, fn ($q) => $q->where('colegio_id', $colegioId))
            ->whereNotNull('materia')
            ->distinct()
            ->pluck('materia');

        return view('biblioteca.index', compact('recursos', 'materias'));
    }

    public function descargar(RecursoDigital $recurso)
    {
        $colegioId = $this->colegioId();

        if ($colegioId && $recurso->colegio_id !== $colegioId) {
            abort(403);
        }

        if (!$recurso->publico) {
            abort(403);
        }

        if (!$recurso->archivo_path || !Storage::disk('local')->exists($recurso->archivo_path)) {
            abort(404, 'Archivo no encontrado.');
        }

        $recurso->increment('descargas');

        return Storage::disk('local')->download($recurso->archivo_path, $recurso->archivo_nombre);
    }
}
