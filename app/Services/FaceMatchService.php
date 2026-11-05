<?php

namespace App\Services;

use App\Models\Citizen;

class FaceMatchService
{
    public const MIN_CONFIDENCE = 0.85;

    /**
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
                    if (! is_array($stored) || count($stored) !== count($embedding)) {
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

    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0; $na = 0.0; $nb = 0.0;
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) {
            $dot += $a[$i] * $b[$i];
            $na  += $a[$i] * $a[$i];
            $nb  += $b[$i] * $b[$i];
        }
        if ($na == 0.0 || $nb == 0.0) return 0.0;
        return $dot / (sqrt($na) * sqrt($nb));
    }
}