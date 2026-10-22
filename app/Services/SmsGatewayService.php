<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SmsGatewayService
{
    public function send(
        string $phone,
        string $message,
        bool $dryRun = false
    ): array {
        $apiKey = config('sms_gateway.api_key');

        $baseUrl = rtrim(
            config('sms_gateway.base_url'),
            '/'
        );

        if (empty($apiKey)) {
            throw new RuntimeException(
                'SMS Gateway API key is not configured.'
            );
        }

        if (
            empty(trim($phone)) ||
            empty(trim($message))
        ) {
            throw new RuntimeException(
                'Phone number and message are required.'
            );
        }

        $response = Http::acceptJson()
            ->withToken($apiKey)
            ->withHeaders([
                'Idempotency-Key' => (string) Str::uuid(),
            ])
            ->connectTimeout(10)
            ->timeout(30)
            ->post($baseUrl . '/messages', [
                'to' => [$phone],
                'text' => $message,
                'type' => 'sms',
                'campaign' => false,
                'dryRun' => $dryRun,
            ]);

        if (!$response->successful()) {
            $errorCode = $response->json('error.code');
            $errorMessage = $response->json('error.message');

            throw new RuntimeException(
                'SMS Gateway request failed. HTTP status: '
                . $response->status()
                . ($errorCode ? ', code: ' . $errorCode : '')
                . ($errorMessage ? ', message: ' . $errorMessage : '')
            );
        }

        return $response->json() ?? [];
    }
}
