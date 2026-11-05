<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceEmbeddingService
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $apiKey,
    ) {}

    /**
     * @return array{success: bool, embedding?: array, error?: string}
     */
    public function extract(string $imageContent, string $filename = 'face.jpg'): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['X-Api-Key' => $this->apiKey])
                ->attach('image', $imageContent, $filename)
                ->post(rtrim($this->baseUrl, '/') . '/extract');

            if ($response->status() === 422) {
                return ['success' => false, 'error' => 'no_face_detected'];
            }

            if (! $response->successful()) {
                Log::error('Face AI failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return ['success' => false, 'error' => 'ai_service_error'];
            }

            return ['success' => true, 'embedding' => $response->json('embedding')];
        } catch (\Throwable $e) {
            Log::error('Face AI exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'ai_service_unavailable'];
        }
    }
}