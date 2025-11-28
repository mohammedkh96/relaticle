<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiToken;
    protected string $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->apiToken = config('services.whatsapp.api_token', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
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
        // If no API token is configured, log the message instead
        if (empty($this->apiToken) || empty($this->phoneNumberId)) {
            Log::info('WhatsApp message (not sent - no API credentials)', [
                'to' => $to,
                'message' => $message,
            ]);

            return [
                'success' => false,
                'message' => 'WhatsApp API not configured. Message logged instead.',
            ];
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
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'response' => $response->json(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => $response->json(),
                ];
            }

            Log::error('WhatsApp message failed', [
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp message exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
            ];
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
        if (empty($this->apiToken) || empty($this->phoneNumberId)) {
            Log::info('WhatsApp template message (not sent - no API credentials)', [
                'to' => $to,
                'template' => $templateName,
                'parameters' => $parameters,
            ]);

            return [
                'success' => false,
                'message' => 'WhatsApp API not configured. Message logged instead.',
            ];
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
                Log::info('WhatsApp template sent successfully', [
                    'to' => $to,
                    'template' => $templateName,
                    'response' => $response->json(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Template sent successfully',
                    'data' => $response->json(),
                ];
            }

            Log::error('WhatsApp template failed', [
                'to' => $to,
                'template' => $templateName,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send template',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp template exception', [
                'to' => $to,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
            ];
        }
    }
}
