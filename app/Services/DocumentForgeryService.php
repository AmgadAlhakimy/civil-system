<?php

namespace App\Services;

class DocumentForgeryService
{
    /**
     * Preliminary image manipulation indicators.
     * This is not a definitive forgery detector.
     */
    public function analyze(string $imagePath): array
    {
        if (
            !is_file($imagePath)
            || !is_readable($imagePath)
            || !function_exists('imagecreatefromstring')
        ) {
            return $this->result(
                'unavailable',
                'تعذر الوصول إلى الصورة أو امتداد GD غير متاح.'
            );
        }

        $info = @getimagesize($imagePath);

        if (
            $info === false
            || !in_array($info['mime'], [
                'image/jpeg',
                'image/png',
                'image/webp',
            ], true)
        ) {
            return $this->result(
                'unavailable',
                'صيغة الصورة غير مدعومة للتحليل.'
            );
        }

        if (($info[0] * $info[1]) > 40000000) {
            return $this->result(
                'warning',
                'أبعاد الصورة كبيرة جدًا للتحليل الأولي.'
            );
        }

        $contents = @file_get_contents($imagePath);
        $image = $contents !== false
            ? @imagecreatefromstring($contents)
            : false;

        if ($image === false) {
            return $this->result(
                'unavailable',
                'تعذر فك ترميز الصورة.'
            );
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $sample = imagecreatetruecolor(32, 32);

        if ($sample === false) {
            imagedestroy($image);

            return $this->result(
                'unavailable',
                'تعذر تجهيز الصورة للتحليل.'
            );
        }

        imagecopyresampled(
            $sample,
            $image,
            0,
            0,
            0,
            0,
            32,
            32,
            $width,
            $height
        );

        $values = [];

        for ($y = 0; $y < 32; $y++) {
            for ($x = 0; $x < 32; $x++) {
                $rgb = imagecolorat($sample, $x, $y);

                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $values[] = (
                    0.299 * $r
                    + 0.587 * $g
                    + 0.114 * $b
                );
            }
        }

        imagedestroy($image);
        imagedestroy($sample);

        $mean = array_sum($values) / count($values);
        $variance = 0;

        foreach ($values as $value) {
            $variance += ($value - $mean) ** 2;
        }

        $contrast = sqrt(
            $variance / count($values)
        );

        // تحليل اختلافات المناطق، وليس كشف التزوير بشكل قطعي.
        $blocks = array_chunk($values, 32);
        $blockMeans = [];

        foreach ($blocks as $block) {
            $blockMeans[] = array_sum($block) / count($block);
        }

        $blockMean = array_sum($blockMeans) / count($blockMeans);
        $blockVariance = 0;

        foreach ($blockMeans as $value) {
            $blockVariance += ($value - $blockMean) ** 2;
        }

        $regionalVariation = sqrt(
            $blockVariance / count($blockMeans)
        );

        $indicators = [];

        if ($contrast < 8) {
            $indicators[] = 'تباين منخفض جدًا.';
        }

        if ($regionalVariation > 65) {
            $indicators[] = 'اختلاف ملحوظ بين مناطق الصورة.';
        }

        return [
            'status' => count($indicators) > 0
                ? 'warning'
                : 'review_required',

            'message' => count($indicators) > 0
                ? 'ظهرت مؤشرات تستحق المراجعة البشرية.'
                : 'لم تظهر مؤشرات غير طبيعية وفق الفحص الأولي، ويلزم التحقق البشري.',

            'analysis' => [
                'contrast' => round($contrast, 2),
                'regional_variation' => round(
                    $regionalVariation,
                    2
                ),
                'indicators' => $indicators,
            ],

            'forgery_confirmed' => false,
            'official_verification' => false,
        ];
    }

    private function result(
        string $status,
        string $message
    ): array {
        return [
            'status' => $status,
            'message' => $message,
            'forgery_confirmed' => false,
            'official_verification' => false,
        ];
    }
}
