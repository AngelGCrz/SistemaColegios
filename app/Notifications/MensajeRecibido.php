<?php

namespace App\Notifications;

use App\Models\Mensaje;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MensajeRecibido extends Notification
{
    use Queueable;

    public function __construct(
        protected Mensaje $mensaje
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nuevo mensaje: {$this->mensaje->asunto}")
            ->greeting("Hola {$notifiable->nombre},")
            ->line("{$this->mensaje->remitente->nombreCompleto()} te ha enviado un mensaje.")
            ->line("**Asunto:** {$this->mensaje->asunto}")
            ->action('Ver Mensaje', url("/mensajes/{$this->mensaje->id}"))
            ->salutation('— ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'mensaje',
            'mensaje_id' => $this->mensaje->id,
            'asunto' => $this->mensaje->asunto,
            'remitente' => $this->mensaje->remitente->nombreCompleto(),
        ];
    }
}
