<?php

namespace App\Services;

use App\Models\DocumentVerification;
use App\Models\User;

class DocumentCrossMatchService
{
    /**
     * Compare OCR fields from the current document with previously
     * submitted documents belonging to the same application user.
     * This is a consistency check, not official identity verification.
     */
    public function compare(
        User $user,
        string $documentType,
        array $ocrResult
    ): array {
        $currentFields = $this->extractFields($ocrResult);

        if ($this->isEmpty($currentFields)) {
            return $this->result(
                'unavailable',
                'لا توجد بيانات كافية مستخرجة من المستند الحالي للمقارنة.'
            );
        }

        $previousVerifications = DocumentVerification::query()
            ->where('user_id', $user->id)
            ->where('document_type', '!=', $documentType)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get(['id', 'document_type', 'checks', 'created_at']);

        $comparisons = [];
        $comparedDocuments = [];

        foreach ($previousVerifications as $verification) {
            $checks = $verification->checks;

            if (is_string($checks)) {
                $decoded = json_decode($checks, true);
                $checks = is_array($decoded) ? $decoded : [];
            }

            if (! is_array($checks)) {
                continue;
            }

            $previousOcr = data_get($checks, 'ocr', []);
            if (! is_array($previousOcr)) {
                continue;
            }

            $previousFields = $this->extractFields($previousOcr);

            if ($this->isEmpty($previousFields)) {
                continue;
            }

            $fieldResults = $this->compareFields(
                $currentFields,
                $previousFields
            );

            if ($fieldResults === []) {
                continue;
            }

            $comparisons[] = [
                'verification_id' => $verification->id,
                'document_type' => $verification->document_type,
                'fields' => $fieldResults,
            ];

            $comparedDocuments[] = $verification->id;
        }

        if ($comparisons === []) {
            return [
                'status' => 'unavailable',
                'message' => 'لا توجد مستندات سابقة تحتوي على بيانات قابلة للمقارنة.',
                'compared_documents' => [],
                'comparisons' => [],
                'official_verification' => false,
            ];
        }

        $hasMismatch = false;
        $matchCount = 0;

        foreach ($comparisons as $comparison) {
            foreach ($comparison['fields'] as $field) {
                if ($field['status'] === 'mismatch') {
                    $hasMismatch = true;
                } elseif ($field['status'] === 'matched') {
                    $matchCount++;
                }
            }
        }

        $status = $hasMismatch
            ? 'warning'
            : ($matchCount > 0 ? 'passed' : 'unavailable');

        return [
            'status' => $status,
            'message' => $hasMismatch
                ? 'توجد اختلافات محتملة بين بيانات المستندات؛ يلزم مراجعتها يدويًا.'
                : ($matchCount > 0
                    ? 'الحقول القابلة للمقارنة متسقة مع المستندات السابقة.'
                    : 'لم تتوفر حقول مشتركة كافية للمقارنة.'),
            'compared_documents' => array_values(array_unique($comparedDocuments)),
            'comparisons' => $comparisons,
            'official_verification' => false,
            'human_review_required' => true,
        ];
    }

    private function extractFields(array $ocrResult): array
    {
        $fields = data_get($ocrResult, 'extracted_fields', []);
        $fields = is_array($fields) ? $fields : [];

        $text = (string) data_get($ocrResult, 'text', '');

        // Fallback extraction for OCR responses that only contain raw text.
        if (empty($fields['national_id']) && preg_match(
            '/(?:رقم\s*الهوية|الرقم\s*الوطني|رقم\s*البطاقة)\s*[:：\-]?\s*([0-9٠-٩]{6,20})/u',
            $text,
            $matches
        )) {
            $fields['national_id'] = $matches[1];
        }

        if (empty($fields['birth_date']) && preg_match(
            '/(?:تاريخ\s*الميلاد|الميلاد)\s*[:：\-]?\s*([0-9٠-٩\/\-.]{6,12})/u',
            $text,
            $matches
        )) {
            $fields['birth_date'] = $matches[1];
        }

        if (empty($fields['name']) && preg_match(
            '/(?:الاسم\s*الكامل|اسم\s*حامل\s*الوثيقة|الاسم)\s*[:：\-]?\s*([^\r\n]+)/u',
            $text,
            $matches
        )) {
            $fields['name'] = trim($matches[1]);
        }

        return [
            'national_id' => $this->normalizeDigits((string) ($fields['national_id'] ?? '')),
            'name' => $this->normalizeName((string) ($fields['name'] ?? '')),
            'birth_date' => $this->normalizeDate((string) ($fields['birth_date'] ?? '')),
        ];
    }

    private function compareFields(array $current, array $previous): array
    {
        $results = [];

        foreach (['national_id', 'name', 'birth_date'] as $field) {
            $currentValue = $current[$field] ?? '';
            $previousValue = $previous[$field] ?? '';

            if ($currentValue === '' || $previousValue === '') {
                continue;
            }

            $matched = $currentValue === $previousValue;

            $results[$field] = [
                'status' => $matched ? 'matched' : 'mismatch',
                'message' => $matched
                    ? 'البيانات متطابقة.'
                    : 'توجد قيمة مختلفة تحتاج إلى مراجعة.',
            ];
        }

        return $results;
    }

    private function normalizeDigits(string $value): string
    {
        $value = strtr($value, [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ]);

        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function normalizeName(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}]/u', '', $value) ?? $value;
        $value = strtr($value, [
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا',
            'ى' => 'ي', 'ؤ' => 'و', 'ئ' => 'ي',
        ]);
        $value = preg_replace('/[^\p{Arabic}\p{Latin}0-9]+/u', '', $value) ?? $value;

        return mb_strtolower($value, 'UTF-8');
    }

    private function normalizeDate(string $value): string
    {
        $digits = $this->normalizeDigits($value);

        // Only compare dates with an unambiguous 8-digit representation.
        if (strlen($digits) !== 8) {
            return '';
        }

        if ((int) substr($digits, 0, 4) >= 1900) {
            return $digits;
        }

        if ((int) substr($digits, 4, 4) >= 1900) {
            return substr($digits, 4, 4)
                . str_pad(substr($digits, 2, 2), 2, '0', STR_PAD_LEFT)
                . str_pad(substr($digits, 0, 2), 2, '0', STR_PAD_LEFT);
        }

        return '';
    }

    private function isEmpty(array $fields): bool
    {
        foreach ($fields as $value) {
            if (is_string($value) && $value !== '') {
                return false;
            }
        }

        return true;
    }

    private function result(string $status, string $message): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'compared_documents' => [],
            'comparisons' => [],
            'official_verification' => false,
        ];
    }
}
