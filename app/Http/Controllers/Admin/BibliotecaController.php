<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecursoDigital;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BibliotecaController extends Controller
{
    use FiltraPorColegio;

    public function index(Request $request)
    {
        $colegioId = $this->colegioId();

        $recursos = RecursoDigital::where('colegio_id', $colegioId)
            ->with('user:id,nombre,apellidos')
            ->when($request->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($request->materia, fn ($q, $v) => $q->where('materia', $v))
            ->when($request->buscar, fn ($q, $v) => $q->where('titulo', 'like', "%{$v}%"))
            ->orderByDesc('created_at')
            ->paginate(12);

        $materias = RecursoDigital::where('colegio_id', $colegioId)
            ->whereNotNull('materia')
            ->distinct()
            ->pluck('materia');

        return view('admin.biblioteca.index', compact('recursos', 'materias'));
    }

    public function create()
    {
        return view('admin.biblioteca.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tipo' => 'required|in:documento,video,enlace,imagen,audio,otro',
            'archivo' => 'nullable|file|max:10240',
            'url_externa' => 'nullable|url|max:500',
            'materia' => 'nullable|string|max:100',
            'nivel' => 'nullable|in:inicial,primaria,secundaria',
            'publico' => 'boolean',
        ]);

        $colegioId = $this->colegioId();

        $data = [
            'colegio_id' => $colegioId,
            'user_id' => auth()->id(),
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'tipo' => $validated['tipo'],
            'url_externa' => $validated['url_externa'] ?? null,
            'materia' => $validated['materia'] ?? null,
            'nivel' => $validated['nivel'] ?? null,
            'publico' => $request->boolean('publico', true),
        ];

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $path = $file->store("recursos/{$colegioId}", 'local');
            $data['archivo_path'] = $path;
            $data['archivo_nombre'] = $file->getClientOriginalName();
        }

        RecursoDigital::create($data);

        return redirect()->route('admin.biblioteca.index')
            ->with('success', 'Recurso creado exitosamente.');
    }

    public function destroy(RecursoDigital $recurso)
    {
        if ($recurso->colegio_id !== $this->colegioId()) {
            abort(403);
        }

        if ($recurso->archivo_path) {
            Storage::disk('local')->delete($recurso->archivo_path);
        }

        $recurso->delete();

        return redirect()->route('admin.biblioteca.index')
            ->with('success', 'Recurso eliminado.');
    }

    public function descargar(RecursoDigital $recurso)
    {
        if ($recurso->colegio_id !== $this->colegioId()) {
            abort(403);
        }

        if (!$recurso->archivo_path || !Storage::disk('local')->exists($recurso->archivo_path)) {
            abort(404, 'Archivo no encontrado.');
        }

        $recurso->increment('descargas');

        return Storage::disk('local')->download($recurso->archivo_path, $recurso->archivo_nombre);
    }
}
