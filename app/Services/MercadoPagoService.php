<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected string $accessToken;
    protected string $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token', '');
    }

    /**
     * Crea una preferencia de pago (checkout).
     */
    public function crearPreferencia(array $datos): ?array
    {
        $response = Http::withToken($this->accessToken)
            ->post("{$this->baseUrl}/checkout/preferences", [
                'items' => [[
                    'title' => $datos['titulo'],
                    'quantity' => 1,
                    'unit_price' => (float) $datos['monto'],
                    'currency_id' => $datos['moneda'] ?? 'USD',
                ]],
                'back_urls' => [
                    'success' => $datos['url_exito'],
                    'failure' => $datos['url_fallo'],
                    'pending' => $datos['url_pendiente'] ?? $datos['url_exito'],
                ],
                'auto_return' => 'approved',
                'external_reference' => $datos['referencia'],
                'notification_url' => $datos['url_webhook'],
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('MercadoPago: Error creando preferencia', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }

    /**
     * Consulta un pago por ID.
     */
    public function obtenerPago(string $pagoId): ?array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/v1/payments/{$pagoId}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Verifica si el servicio está configurado.
     */
    public function estaConfigurado(): bool
    {
        return !empty($this->accessToken);
    }
}
