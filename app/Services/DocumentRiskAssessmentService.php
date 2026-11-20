<?php

namespace App\Services;

class DocumentRiskAssessmentService
{
    /**
     * Rule-based preliminary risk assessment.
     * Unavailable and not-applicable checks are excluded from scoring.
     */
    public function assess(array $checks): array
    {
        $weights = [
            'image_readability' => 15,
            'file_integrity' => 12,
            'image_dimensions' => 6,
            'image_quality' => 7,
            'duplicate_detection' => 15,
            'forgery_detection' => 15,
            'face_matching' => 10,
            'cross_document_matching' => 10,
            'mrz_verification' => 6,
            'qr_verification' => 4,
        ];

        $weightedRisk = 0.0;
        $applicableWeight = 0;
        $reasons = [];
        $evaluatedChecks = [];

        foreach ($weights as $key => $weight) {
            $check = $checks[$key] ?? null;

            if (!is_array($check)) {
                continue;
            }

            $status = $check['status'] ?? 'unavailable';

            if (in_array($status, ['unavailable', 'not_applicable'], true)) {
                continue;
            }

            if (!in_array($status, ['passed', 'failed', 'warning', 'review_required'], true)) {
                continue;
            }

            $applicableWeight += $weight;
            $evaluatedChecks[] = $key;

            if ($status === 'failed') {
                $weightedRisk += $weight;
                $reasons[] = "فشل الفحص: {$key}";
            } elseif (in_array($status, ['warning', 'review_required'], true)) {
                $weightedRisk += $weight * 0.5;
                $reasons[] = "الفحص يحتاج إلى مراجعة: {$key}";
            }
        }

        if ($applicableWeight === 0) {
            return [
                'status' => 'insufficient_data',
                'method' => 'rule_based',
                'risk_score' => null,
                'risk_level' => 'undetermined',
                'reasons' => [],
                'evaluated_checks' => [],
                'recommendation' => 'لا تتوفر نتائج كافية لتقييم المخاطر. يلزم استكمال الفحوص.',
                'official_verification' => false,
                'forgery_confirmed' => false,
            ];
        }

        $score = (int) round(
            ($weightedRisk / $applicableWeight) * 100
        );

        if ($score >= 50) {
            $level = 'high';
            $recommendation = 'إحالة المستند إلى موظف مختص للمراجعة.';
        } elseif ($score >= 20) {
            $level = 'medium';
            $recommendation = 'مراجعة نتائج الفحص قبل متابعة الطلب.';
        } else {
            $level = 'low';
            $recommendation = 'المؤشرات المتاحة منخفضة المخاطر، مع ضرورة استكمال التحقق المطلوب.';
        }

        return [
            'status' => 'completed',
            'method' => 'rule_based',
            'risk_score' => $score,
            'risk_level' => $level,
            'reasons' => $reasons,
            'evaluated_checks' => $evaluatedChecks,
            'recommendation' => $recommendation,
            'official_verification' => false,
            'forgery_confirmed' => false,
            'message' => 'تقييم آلي أولي قائم على قواعد، وليس حكمًا نهائيًا على أصالة المستند.',
        ];
    }
}
