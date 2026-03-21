<?php

namespace App\Http\Controllers;

use App\Models\EntregaTarea;
use App\Models\Tarea;
use App\Traits\FiltraPorColegio;
use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    use FiltraPorColegio;

    public function descargar(string $tipo, int $id)
    {
        $colegioId = $this->colegioId();

        if ($tipo === 'tarea') {
            $tarea = Tarea::where('id', $id)->where('colegio_id', $colegioId)->firstOrFail();
            $path = $tarea->archivo_adjunto;
        } elseif ($tipo === 'entrega') {
            $entrega = EntregaTarea::where('id', $id)->where('colegio_id', $colegioId)->firstOrFail();
            $path = $entrega->archivo;
        } else {
            abort(404);
        }

        abort_if(!$path || !Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}
