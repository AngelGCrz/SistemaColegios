<?php

namespace App\Notifications;

use App\Models\Pago;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoRegistrado extends Notification
{
    use Queueable;

    public function __construct(
        protected Pago $pago
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (!in_array(config('mail.default'), ['log', 'array'])) {
            $channels[] = 'mail';
        }

        if (config('services.whatsapp.token') && $notifiable->telefono) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $alumno = $this->pago->alumno;

        return (new MailMessage)
            ->subject("Nuevo cargo registrado — S/ {$this->pago->monto}")
            ->greeting("Hola {$notifiable->nombre},")
            ->line("Se ha registrado un nuevo cargo para el alumno **{$alumno->user->apellidos}, {$alumno->user->nombre}**.")
            ->line("**Concepto:** {$this->pago->conceptoPago->nombre}")
            ->line("**Monto:** S/ " . number_format($this->pago->monto, 2))
            ->line("**Estado:** " . ucfirst($this->pago->estado))
            ->salutation('— ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'pago',
            'pago_id' => $this->pago->id,
            'concepto' => $this->pago->conceptoPago->nombre,
            'monto' => $this->pago->monto,
            'estado' => $this->pago->estado,
            'alumno' => $this->pago->alumno->user->nombreCompleto(),
        ];
    }

    public function toWhatsApp(object $notifiable): array
    {
        return [
            'message' => "Nuevo cargo registrado: S/ {$this->pago->monto} - {$this->pago->conceptoPago->nombre}. Estado: {$this->pago->estado}.",
        ];
    }
}
