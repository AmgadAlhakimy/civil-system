<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Throwable;

class DocumentQrService
{
    public function verify(string $imagePath): array
    {
        if (!is_file($imagePath) || !is_readable($imagePath)) {
            return $this->result(
                'unavailable',
                'ملف الصورة غير متاح.'
            );
        }

        try {
            $binary = env(
                'ZBARIMG_PATH',
                'zbarimg'
            );

            $process = new Process([
                $binary,
                '--quiet',
                '--raw',
                $imagePath,
            ]);

            $process->setTimeout(20);
            $process->run();

            if (!$process->isSuccessful()) {
                return $this->result(
                    'warning',
                    'لم يتم العثور على رمز QR قابل للقراءة.'
                );
            }

            $contents = trim($process->getOutput());

            if ($contents === '') {
                return $this->result(
                    'warning',
                    'رمز QR فارغ أو غير قابل للقراءة.'
                );
            }

            $isUrl = filter_var(
                $contents,
                FILTER_VALIDATE_URL
            ) !== false;

            return [
                'status' => 'passed',
                'message' => 'تم اكتشاف رمز QR وقراءة محتواه.',
                'qr_detected' => true,
                'content' => $contents,
                'content_type' => $isUrl ? 'url' : 'text',
                'official_verification' => false,
                'trusted_source' => false,
            ];

        } catch (Throwable $exception) {
            report($exception);

            return $this->result(
                'unavailable',
                'خدمة قراءة QR غير متاحة حاليًا.'
            );
        }
    }

    private function result(
        string $status,
        string $message
    ): array {
        return [
            'status' => $status,
            'message' => $message,
            'qr_detected' => false,
            'content' => null,
            'content_type' => null,
            'official_verification' => false,
            'trusted_source' => false,
        ];
    }
}
