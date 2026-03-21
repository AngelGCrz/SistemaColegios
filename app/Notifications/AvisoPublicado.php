<?php

namespace App\Notifications;

use App\Models\Aviso;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AvisoPublicado extends Notification
{
    use Queueable;

    public function __construct(
        protected Aviso $aviso
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nuevo aviso: {$this->aviso->titulo}")
            ->greeting("Hola {$notifiable->nombre},")
            ->line("Se ha publicado un nuevo aviso en tu colegio.")
            ->line("**{$this->aviso->titulo}**")
            ->line($this->aviso->contenido)
            ->salutation('— ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'aviso',
            'aviso_id' => $this->aviso->id,
            'titulo' => $this->aviso->titulo,
        ];
    }
}
