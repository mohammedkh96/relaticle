<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $apiUrl;
    protected ?string $apiToken;
    protected ?string $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url') ?: 'https://graph.facebook.com/v18.0';
        $this->apiToken = config('services.whatsapp.api_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    protected function isConfigured(): bool
    {
        return !empty($this->apiToken) && !empty($this->phoneNumberId);
    }

    /**
     * Send a WhatsApp message
     *
     * @param string $to Phone number in international format (e.g., +1234567890)
     * @param string $message Message content
     * @return array Response from WhatsApp API
     */
    public function sendMessage(string $to, string $message): array
    {
        // Check if API is configured
        if (!$this->isConfigured()) {
            throw new \Exception('WhatsApp API not configured. Please configure API credentials in Communication Settings.');
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            $error = $response->json('error.message', 'Unknown error occurred');
            throw new \Exception("WhatsApp API Error: {$error}");

        } catch (\Exception $e) {
            Log::error('WhatsApp send error', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send a template message
     *
     * @param string $to Phone number
     * @param string $templateName Template name
     * @param array $parameters Template parameters
     * @return array Response
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = []): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('WhatsApp API not configured. Please configure API credentials in Communication Settings.');
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en',
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => $parameters,
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            $error = $response->json('error.message', 'Unknown error occurred');
            throw new \Exception("WhatsApp Template Error: {$error}");

        } catch (\Exception $e) {
            Log::error('WhatsApp template error', [
                'to' => $to,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
