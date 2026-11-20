<?php

namespace App\Services;

class DocumentMrzService
{
    /**
     * Validate passport MRZ structure and check digits.
     * Supports TD3 passport format (2 lines x 44 characters).
     */
    public function verify(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', strtoupper($text));

        $candidates = [];

        foreach ($lines as $line) {
            $line = preg_replace('/[^A-Z0-9<]/', '', $line);
            
            if (strlen($line) === 44) {
                $candidates[] = $line;
            }
        }

        if (count($candidates) < 2) {
            return $this->result(
                'warning',
                'لم يتم العثور على سطري MRZ بطول 44 حرفًا.'
            );
        }

        $line1 = $candidates[0];
        $line2 = $candidates[1];

        $checks = [
            'document_number' => $this->checkDigit(
                substr($line2, 0, 9),
                $line2[9]
            ),

            'birth_date' => $this->checkDigit(
                substr($line2, 13, 6),
                $line2[19]
            ),

            'expiry_date' => $this->checkDigit(
                substr($line2, 21, 6),
                $line2[27]
            ),

            'personal_number' => (
                $line2[42] === '<'
                || $this->checkDigit(
                    substr($line2, 28, 14),
                    $line2[42]
                )
            ),

            'composite' => $this->checkDigit(
                substr($line2, 0, 10)
                . substr($line2, 13, 7)
                . substr($line2, 21, 22),
                $line2[43]
            ),
        ];

        $allPassed = !in_array(false, $checks, true);

        return [
            'status' => $allPassed ? 'passed' : 'warning',

            'message' => $allPassed
                ? 'اجتازت بيانات MRZ فحوص البنية وأرقام التحقق.'
                : 'تم العثور على MRZ ولكن بعض أرقام التحقق غير صحيحة.',

            'format' => 'TD3',

            'structure_valid' => true,

            'checks' => $checks,

            'document_number' => $this->clean(
                substr($line2, 0, 9)
            ),

            'birth_date' => substr($line2, 13, 6),

            'expiry_date' => substr($line2, 21, 6),

            'surname_and_names' => $this->clean(
                substr($line1, 5)
            ),

            'official_verification' => false,
        ];
    }

    private function checkDigit(
        string $value,
        string $expected
    ): bool {
        if (!ctype_digit($expected)) {
            return false;
        }

        $weights = [7, 3, 1];
        $sum = 0;

        foreach (str_split($value) as $index => $character) {
            if ($character === '<') {
                $number = 0;
            } elseif (ctype_digit($character)) {
                $number = (int) $character;
            } elseif ($character >= 'A' && $character <= 'Z') {
                $number = ord($character) - 55;
            } else {
                return false;
            }

            $sum += $number * $weights[$index % 3];
        }

        return ($sum % 10) === (int) $expected;
    }

    private function clean(string $value): string
    {
        return trim(str_replace('<', ' ', $value));
    }

    private function result(
        string $status,
        string $message
    ): array {
        return [
            'status' => $status,
            'message' => $message,
            'structure_valid' => false,
            'official_verification' => false,
        ];
    }
}
