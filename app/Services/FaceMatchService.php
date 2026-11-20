<?php

namespace App\Services;

use App\Models\Citizen;

class FaceMatchService
{
    public const MIN_CONFIDENCE = 0.85;

    /**
     * Existing 1:N identification method. Keep this for the face identify API.
     * @return array{citizen: ?Citizen, confidence: float}
     */
    public function findBestMatch(array $embedding): array
    {
        $best = null;
        $bestScore = 0.0;

        Citizen::query()
            ->whereNotNull('face_data')
            ->where('face_data', '!=', '')
            ->select(['id', 'face_data'])
            ->chunkById(500, function ($citizens) use ($embedding, &$best, &$bestScore) {
                foreach ($citizens as $citizen) {
                    $stored = json_decode($citizen->face_data, true);

                    if (! $this->validEmbedding($stored)
                        || count($stored) !== count($embedding)) {
                        continue;
                    }

                    $score = $this->cosineSimilarity($embedding, $stored);

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $best = $citizen->id;
                    }
                }
            });

        if ($best === null || $bestScore < self::MIN_CONFIDENCE) {
            return ['citizen' => null, 'confidence' => $bestScore];
        }

        return [
            'citizen' => Citizen::find($best),
            'confidence' => $bestScore,
        ];
    }

    /**
     * 1:1 comparison against the citizen already identified by a document number.
     * This avoids searching all citizens during document verification.
     * @return array{matched: bool, confidence: float}
     */
    public function compareWithCitizen(array $embedding, Citizen $citizen): array
    {
        $stored = json_decode((string) $citizen->face_data, true);

        if (! $this->validEmbedding($stored)
            || count($stored) !== count($embedding)
            || ! $this->validEmbedding($embedding)) {
            return ['matched' => false, 'confidence' => 0.0];
        }

        $score = $this->cosineSimilarity($embedding, $stored);

        return [
            'matched' => $score >= self::MIN_CONFIDENCE,
            'confidence' => round($score, 4),
        ];
    }

    private function validEmbedding($embedding): bool
    {
        if (! is_array($embedding) || count($embedding) === 0) {
            return false;
        }

        foreach ($embedding as $value) {
            if (! is_numeric($value) || ! is_finite((float) $value)) {
                return false;
            }
        }

        return true;
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $na = 0.0;
        $nb = 0.0;

        for ($i = 0; $i < count($a); $i++) {
            $av = (float) $a[$i];
            $bv = (float) $b[$i];

            $dot += $av * $bv;
            $na += $av * $av;
            $nb += $bv * $bv;
        }

        if ($na == 0.0 || $nb == 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($na) * sqrt($nb));
    }
}
