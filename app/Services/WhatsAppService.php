<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl;
    private string $token;
    private string $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = 'https://graph.facebook.com/v18.0';
        $this->token = config('services.whatsapp.token', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
    }

    /**
     * Check if WhatsApp is configured and enabled.
     */
    public function isEnabled(): bool
    {
        return !empty($this->token) && !empty($this->phoneNumberId);
    }

    /**
     * Send a template message via WhatsApp Business API.
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = [], string $language = 'es'): bool
    {
        if (!$this->isEnabled()) {
            Log::info('WhatsApp no configurado. Mensaje no enviado a: ' . $to);
            return false;
        }

        $components = [];
        if (!empty($parameters)) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(fn ($p) => [
                    'type' => 'text',
                    'text' => (string) $p,
                ], $parameters),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $language],
                'components' => $components,
            ],
        ];

        return $this->send($payload);
    }

    /**
     * Send a text message via WhatsApp Business API.
     */
    public function sendText(string $to, string $message): bool
    {
        if (!$this->isEnabled()) {
            Log::info('WhatsApp no configurado. Mensaje no enviado a: ' . $to);
            return false;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($to),
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ];

        return $this->send($payload);
    }

    private function send(array $payload): bool
    {
        try {
            $response = Http::withToken($this->token)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                Log::info('WhatsApp mensaje enviado', [
                    'to' => $payload['to'],
                    'type' => $payload['type'],
                ]);
                return true;
            }

            Log::warning('WhatsApp error al enviar', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp excepción: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number to international format (Peru default).
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, remove it
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        // If 9 digits (Peru mobile), add country code
        if (strlen($phone) === 9 && str_starts_with($phone, '9')) {
            $phone = '51' . $phone;
        }

        return $phone;
    }
}
