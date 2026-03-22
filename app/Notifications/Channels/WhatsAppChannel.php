<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function __construct(private WhatsAppService $whatsapp)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if (!$this->whatsapp->isEnabled()) {
            return;
        }

        $phone = $notifiable->telefono ?? $notifiable->routeNotificationFor('whatsapp');

        if (empty($phone)) {
            return;
        }

        $data = $notification->toWhatsApp($notifiable);

        if (isset($data['template'])) {
            $this->whatsapp->sendTemplate(
                $phone,
                $data['template'],
                $data['parameters'] ?? [],
                $data['language'] ?? 'es'
            );
        } elseif (isset($data['message'])) {
            $this->whatsapp->sendText($phone, $data['message']);
        }
    }
}
