<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Throwable;

class DocumentOcrService
{
    public function extract(string $imagePath): array
    {
        $binary = env(
            'TESSERACT_PATH',
            'tesseract'
        );

        if (!is_file($imagePath) || !is_readable($imagePath)) {
            return $this->unavailable('ملف الصورة غير متاح.');
        }

        try {
            $process = new Process([
                $binary,
                $imagePath,
                'stdout',
                '-l',
                'ara+eng',
                '--psm',
                '6',
                'tsv',
            ]);

            $process->setTimeout(45);
            $process->run();

            if (!$process->isSuccessful()) {
                return $this->unavailable(
                    'تعذر تشغيل محرك Tesseract OCR.'
                );
            }

            $lines = preg_split(
                '/\r\n|\r|\n/',
                trim($process->getOutput())
            );

            $recognizedLines = [];
            $confidences = [];

            foreach ($lines as $index => $line) {
                if ($index === 0 || trim($line) === '') {
                    continue;
                }

                $columns = explode("\t", $line, 12);

                if (count($columns) < 12) {
                    continue;
                }

                $word = trim($columns[11]);

                $confidence = is_numeric($columns[10])
                    ? (float) $columns[10]
                    : -1;

                if ($word !== '') {
                    $lineKey = implode('-', [
                        $columns[1],
                        $columns[2],
                        $columns[3],
                        $columns[4],
                    ]);

                    $recognizedLines[$lineKey][] = $word;
                }

                if ($confidence >= 0) {
                    $confidences[] = $confidence;
                }
            }

            $textLines = [];

            foreach ($recognizedLines as $words) {
                $textLines[] = implode(' ', $words);
            }

            $text = trim(implode("\n", $textLines));

            $fields = $this->extractFields($text);

            $averageConfidence = count($confidences) > 0
                ? round(
                    array_sum($confidences) / count($confidences),
                    2
                )
                : null;

            return [
                'status' => $text !== '' ? 'passed' : 'warning',

                'message' => $text !== ''
                    ? 'تم استخراج النص والبيانات المحتملة من المستند.'
                    : 'لم يتم استخراج نص واضح من الصورة.',

                'text' => $text,

                'extracted_fields' => $fields,

                'languages' => ['ara', 'eng'],

                'average_confidence' => $averageConfidence,

                'official_verification' => false,
            ];

        } catch (Throwable $exception) {
            report($exception);

            return $this->unavailable(
                'حدث خطأ أثناء استخراج النص من المستند.'
            );
        }
    }

    /**
     * استخراج الحقول المحتملة من النص.
     * لا يعني ذلك التحقق من صحة البيانات.
     */
    private function extractFields(string $text): array
    {
        $normalizedText = $this->normalizeDigits($text);

        $lines = preg_split(
            '/\r\n|\r|\n/',
            $normalizedText
        );

        $fields = [
            'name' => null,
            'national_id' => null,
            'birth_date' => null,
            'issue_date' => null,
            'expiry_date' => null,
            'number_candidates' => [],
            'date_candidates' => [],
        ];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            // استخراج الاسم من السطر الذي يحتوي على تسمية الاسم
            if (
                $fields['name'] === null
                && preg_match(
                    '/(?:الاسم|اسم حامل الوثيقة|الاسم الكامل)\s*[:：\-]?\s*(.+)/u',
                    $line,
                    $matches
                )
            ) {
                $fields['name'] = trim($matches[1]);
            }

            // استخراج رقم الهوية عند وجود تسمية واضحة
            if (
                $fields['national_id'] === null
                && preg_match(
                    '/(?:رقم الهوية|الرقم الوطني|رقم البطاقة|رقم القيد)\s*[:：\-]?\s*([0-9٠-٩]{6,20})/u',
                    $line,
                    $matches
                )
            ) {
                $fields['national_id'] = $this->normalizeDigits(
                    $matches[1]
                );
            }

            // البحث عن التواريخ المرتبطة بتسمياتها
            if (
                preg_match(
                    '/(?:تاريخ الميلاد|الميلاد)\s*[:：\-]?\s*([0-9٠-٩\/\-.]{6,12})/u',
                    $line,
                    $matches
                )
            ) {
                $fields['birth_date'] = $matches[1];
            }

            if (
                preg_match(
                    '/(?:تاريخ الإصدار|تاريخ الصدور|تاريخ التحرير)\s*[:：\-]?\s*([0-9٠-٩\/\-.]{6,12})/u',
                    $line,
                    $matches
                )
            ) {
                $fields['issue_date'] = $matches[1];
            }

            if (
                preg_match(
                    '/(?:تاريخ الانتهاء|تاريخ انتهاء الصلاحية|صالح حتى)\s*[:：\-]?\s*([0-9٠-٩\/\-.]{6,12})/u',
                    $line,
                    $matches
                )
            ) {
                $fields['expiry_date'] = $matches[1];
            }
        }

        // جمع الأرقام المرشحة لمراجعتها لاحقًا
        preg_match_all(
            '/(?<![0-9])[0-9]{6,20}(?![0-9])/',
            $this->normalizeDigits($normalizedText),
            $numberMatches
        );

        $fields['number_candidates'] = array_values(
            array_unique($numberMatches[0] ?? [])
        );

        // جمع التواريخ المحتملة
        preg_match_all(
            '/(?<![0-9])[0-9]{1,4}[\/\-.][0-9]{1,2}[\/\-.][0-9]{2,4}(?![0-9])/',
            $this->normalizeDigits($normalizedText),
            $dateMatches
        );

        $fields['date_candidates'] = array_values(
            array_unique($dateMatches[0] ?? [])
        );

        return $fields;
    }

    /**
     * تحويل الأرقام العربية والهندية إلى أرقام لاتينية.
     */
    private function normalizeDigits(string $text): string
    {
        return strtr($text, [
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9',

            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
        ]);
    }

    private function unavailable(string $message): array
    {
        return [
            'status' => 'unavailable',
            'message' => $message,
            'text' => null,
            'extracted_fields' => [],
            'languages' => ['ara', 'eng'],
            'average_confidence' => null,
            'official_verification' => false,
        ];
    }
}
