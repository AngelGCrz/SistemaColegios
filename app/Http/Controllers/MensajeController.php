<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\User;
use App\Notifications\MensajeRecibido;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    use FiltraPorColegio;

    public function inbox()
    {
        $mensajes = Mensaje::where('colegio_id', $this->colegioId())
            ->where('destinatario_id', auth()->id())
            ->with('remitente')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('mensajes.inbox', compact('mensajes'));
    }

    public function enviados()
    {
        $mensajes = Mensaje::where('colegio_id', $this->colegioId())
            ->where('remitente_id', auth()->id())
            ->with('destinatario')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('mensajes.enviados', compact('mensajes'));
    }

    public function create()
    {
        $usuarios = User::where('colegio_id', $this->colegioId())
            ->where('id', '!=', auth()->id())
            ->where('activo', true)
            ->orderBy('apellidos')
            ->get();

        return view('mensajes.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'destinatario_id' => ['required', 'exists:users,id'],
            'asunto' => ['required', 'string', 'max:200'],
            'contenido' => ['required', 'string', 'max:5000'],
        ]);

        // Verificar que el destinatario pertenece al mismo colegio
        $destinatario = User::where('id', $data['destinatario_id'])
            ->where('colegio_id', $this->colegioId())
            ->firstOrFail();

        $mensaje = Mensaje::create([
            'colegio_id' => $this->colegioId(),
            'remitente_id' => auth()->id(),
            ...$data,
        ]);

        // Notificar al destinatario por email
        $destinatario->notify(new MensajeRecibido($mensaje));

        return redirect()->route('mensajes.enviados')
            ->with('success', 'Mensaje enviado exitosamente.');
    }

    public function show(Mensaje $mensaje)
    {
        abort_if($mensaje->colegio_id !== $this->colegioId(), 403);

        // Solo el remitente o destinatario pueden ver el mensaje
        abort_if(
            $mensaje->remitente_id !== auth()->id() &&
            $mensaje->destinatario_id !== auth()->id(),
            403
        );

        // Marcar como leído si es el destinatario
        if ($mensaje->destinatario_id === auth()->id() && !$mensaje->leido) {
            $mensaje->marcarLeido();
        }

        return view('mensajes.show', compact('mensaje'));
    }
}
