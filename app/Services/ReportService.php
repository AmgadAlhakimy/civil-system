<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BirthCertificate;
use App\Models\Citizen;
use App\Models\DeathCertificate;
use App\Models\FamilyCard;
use App\Models\IdentityCard;
use App\Models\Passport;
use App\Models\Report;
use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class ReportService
{
    public function generate(
        string $reportType,
        string $format,
        array $filters = [],
        ?string $name = null
    ): Report {
        $report = Report::create([
            'name' => trim($name ?? '') ?: $this->getReportName($reportType),
            'report_type' => $reportType,
            'format' => $format,
            'user_id' => Auth::id(),
            'filters' => $filters ?: null,
            'status' => 'pending',
        ]);

        try {
            [$headers, $rows] = $this->getData($reportType, $filters);

            if ($format === 'pdf') {
                $path = $this->generatePdf($report, $headers, $rows);
            } elseif ($format === 'xlsx') {
                $path = $this->generateExcel($report, $headers, $rows);
            } else {
                throw new InvalidArgumentException('صيغة التقرير غير مدعومة.');
            }

            $report->update([
                'path' => $path,
                'size' => Storage::disk('local')->size($path),
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            return $report->refresh();
        } catch (Throwable $exception) {
            $report->update([
                'status' => 'failed',
            ]);

            throw $exception;
        }
    }

    protected function getData(string $reportType, array $filters = []): array
    {
        return match ($reportType) {
            'citizens' => [
                [
                    'الاسم الكامل',
                    'اسم الأب',
                    'اسم الأم',
                    'تاريخ الميلاد',
                    'مكان الميلاد',
                    'الجنس',
                    'الحالة الاجتماعية',
                    'المهنة',
                    'الهاتف',
                    'العنوان',
                    'الحالة',
                ],
                $this->citizens($filters),
            ],

            'passports' => [
                [
                    'رقم الجواز',
                    'الرقم الوطني',
                    'اسم المواطن',
                    'نوع الجواز',
                    'تاريخ الإصدار',
                    'تاريخ الانتهاء',
                    'الحالة',
                ],
                $this->passports($filters),
            ],

            'identity_cards' => [
                [
                    'الرقم الوطني',
                    'اسم المواطن',
                    'تاريخ الإصدار',
                    'تاريخ الانتهاء',
                    'الحالة',
                ],
                $this->identityCards($filters),
            ],

            'family_cards' => [
                [
                    'رقم البطاقة',
                    'الرقم الوطني لرب الأسرة',
                    'اسم رب الأسرة',
                    'تاريخ الإصدار',
                    'تاريخ الانتهاء',
                    'الحالة',
                ],
                $this->familyCards($filters),
            ],

            'birth_certificates' => [
                [
                    'اسم الطفل',
                    'اسم الأب',
                    'اسم الأم',
                    'تاريخ الإصدار',
                    'الحالة',
                ],
                $this->birthCertificates($filters),
            ],

            'death_certificates' => [
                [
                    'اسم المتوفى',
                    'تاريخ الوفاة',
                    'سبب الوفاة',
                    'مكان الوفاة',
                    'تاريخ الإصدار',
                    'الحالة',
                ],
                $this->deathCertificates($filters),
            ],

            'appointments' => [
                [
                    'المواطن',
                    'الرقم الوطني',
                    'نوع الخدمة',
                    'الفرع',
                    'الموظف',
                    'تاريخ الموعد',
                    'وقت الموعد',
                    'الحالة',
                ],
                $this->appointments($filters),
            ],

            default => throw new InvalidArgumentException('نوع التقرير غير مدعوم.'),
        };
    }

    protected function applyDateRange(
        Builder $query,
        array $filters,
        string $column
    ): Builder {
        if (! empty($filters['from_date'])) {
            $query->whereDate($column, '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate($column, '<=', $filters['to_date']);
        }

        return $query;
    }

    protected function citizens(array $filters = []): array
    {
        $query = Citizen::query();

        if (! empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (! empty($filters['marital_status'])) {
            $query->where('marital_status', $filters['marital_status']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        $this->applyDateRange($query, $filters, 'created_at');

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Citizen $citizen) => [
                $this->citizenFullName($citizen),
                $citizen->father_name,
                $citizen->mother_name,
                $citizen->birth_date?->format('Y-m-d'),
                $citizen->birth_place,
                $this->genderLabel($citizen->gender),
                $this->maritalStatusLabel($citizen->marital_status),
                $citizen->occupation,
                $citizen->phone,
                $citizen->address,
                $citizen->is_active ? 'نشط' : 'غير نشط',
            ])
            ->toArray();
    }

    protected function passports(array $filters = []): array
    {
        $query = Passport::query()->with('citizen');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $this->applyDateRange($query, $filters, 'issue_date');

        return $query->orderByDesc('issue_date')
            ->get()
            ->map(fn (Passport $passport) => [
                $passport->passport_number,
                $this->nationalIdForCitizen($passport->citizen_id),
                $this->citizenFullName($passport->citizen),
                $this->passportTypeLabel($passport->type),
                $passport->issue_date?->format('Y-m-d'),
                $passport->expiry_date?->format('Y-m-d'),
                $this->documentStatusLabel($passport->status),
            ])
            ->toArray();
    }

    protected function identityCards(array $filters = []): array
    {
        $query = IdentityCard::query()->with('citizen');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $this->applyDateRange($query, $filters, 'issue_date');

        return $query->orderByDesc('issue_date')
            ->get()
            ->map(fn (IdentityCard $card) => [
                $card->national_id,
                $this->citizenFullName($card->citizen),
                $card->issue_date?->format('Y-m-d'),
                $card->expiry_date?->format('Y-m-d'),
                $this->documentStatusLabel($card->status),
            ])
            ->toArray();
    }

    protected function familyCards(array $filters = []): array
    {
        $query = FamilyCard::query()->with('head');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $this->applyDateRange($query, $filters, 'issue_date');

        return $query->orderByDesc('issue_date')
            ->get()
            ->map(fn (FamilyCard $card) => [
                $card->card_number,
                $this->nationalIdForCitizen($card->head_id),
                $this->citizenFullName($card->head),
                $card->issue_date?->format('Y-m-d'),
                $card->expiry_date?->format('Y-m-d'),
                $this->documentStatusLabel($card->status),
            ])
            ->toArray();
    }

    protected function birthCertificates(array $filters = []): array
    {
        $query = BirthCertificate::query()
            ->with(['child', 'father', 'mother']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $this->applyDateRange($query, $filters, 'issue_date');

        return $query->orderByDesc('issue_date')
            ->get()
            ->map(fn (BirthCertificate $certificate) => [
                $this->citizenFullName($certificate->child),
                $this->citizenFullName($certificate->father),
                $this->citizenFullName($certificate->mother),
                $certificate->issue_date?->format('Y-m-d'),
                $this->documentStatusLabel($certificate->status),
            ])
            ->toArray();
    }

    protected function deathCertificates(array $filters = []): array
    {
        $query = DeathCertificate::query()->with('deceased');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $this->applyDateRange($query, $filters, 'issue_date');

        return $query->orderByDesc('issue_date')
            ->get()
            ->map(fn (DeathCertificate $certificate) => [
                $this->citizenFullName($certificate->deceased),
                $certificate->death_date?->format('Y-m-d'),
                $certificate->cause_of_death,
                $certificate->place_of_death,
                $certificate->issue_date?->format('Y-m-d'),
                $this->documentStatusLabel($certificate->status),
            ])
            ->toArray();
    }

    protected function appointments(array $filters = []): array
    {
        $query = Appointment::query()
            ->with(['citizen', 'branch', 'user']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        $this->applyDateRange($query, $filters, 'appointment_date');

        return $query->orderByDesc('appointment_date')
            ->get()
            ->map(fn (Appointment $appointment) => [
                $this->citizenFullName($appointment->citizen),
                $this->nationalIdForCitizen($appointment->citizen_id),
                $this->serviceTypeLabel($appointment->service_type),
                $appointment->branch?->name ?? '-',
                $appointment->user?->name ?? '-',
                $appointment->appointment_date?->format('Y-m-d'),
                $appointment->appointment_time,
                $this->appointmentStatusLabel($appointment->status),
            ])
            ->toArray();
    }

    protected function nationalIdForCitizen(?string $citizenId): string
    {
        if (! $citizenId) {
            return '-';
        }

        $activeCard = IdentityCard::query()
            ->where('citizen_id', $citizenId)
            ->where('status', 'active')
            ->whereDate('expiry_date', '>=', today())
            ->orderByDesc('issue_date')
            ->first();

        if ($activeCard) {
            return $activeCard->national_id ?: '-';
        }

        $latestCard = IdentityCard::query()
            ->where('citizen_id', $citizenId)
            ->orderByDesc('issue_date')
            ->first();

        return $latestCard?->national_id ?: '-';
    }

    protected function citizenFullName(?Citizen $citizen): string
    {
        if (! $citizen) {
            return '-';
        }

        return collect([
            $citizen->first_name,
            $citizen->father_name,
            $citizen->middle_name,
            $citizen->last_name,
        ])->filter()->implode(' ') ?: '-';
    }

    protected function genderLabel(?string $gender): string
    {
        return match ($gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => $gender ?: '-',
        };
    }

    protected function maritalStatusLabel(?string $status): string
    {
        return match ($status) {
            'single' => 'أعزب',
            'married' => 'متزوج',
            'divorced' => 'مطلق',
            'widowed' => 'أرمل',
            default => $status ?: '-',
        };
    }

    protected function passportTypeLabel(?string $type): string
    {
        return match ($type) {
            'ordinary' => 'عادي',
            'diplomatic' => 'دبلوماسي',
            'official' => 'رسمي',
            default => $type ?: '-',
        };
    }

    protected function documentStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'قيد الانتظار',
            'rejected' => 'مرفوض',
            'active' => 'ساري',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
            'lost' => 'مفقود',
            'damaged' => 'تالف',
            default => $status ?: '-',
        };
    }

    protected function appointmentStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'rejected' => 'مرفوض',
            'missed' => 'لم يحضر',
            default => $status ?: '-',
        };
    }

    protected function serviceTypeLabel(?string $type): string
    {
        return match ($type) {
            'citizen_registration' => 'تسجيل مواطن',
            'passport' => 'إصدار جواز سفر',
            'identity_card' => 'إصدار بطاقة شخصية',
            'family_card' => 'إصدار بطاقة عائلية',
            'birth_certificate' => 'شهادة ميلاد',
            'death_certificate' => 'شهادة وفاة',
            default => $type ?: '-',
        };
    }

    protected function arabicText(mixed $text): string
    {
        if ($text === null || $text === '') {
            return '-';
        }

        $text = (string) $text;

        if (! preg_match('/\p{Arabic}/u', $text)) {
            return $text;
        }

        return (new Arabic())->utf8Glyphs($text);
    }

    protected function generatePdf(
        Report $report,
        array $headers,
        array $rows
    ): string {
        $pdfHeaders = array_map(
            fn ($header) => $this->arabicText($header),
            $headers
        );

        $pdfRows = array_map(
            fn ($row) => array_map(
                fn ($value) => $this->arabicText($value),
                $row
            ),
            $rows
        );

        $labels = [
            'report_date' => 'تاريخ إنشاء التقرير',
            'format' => 'صيغة التقرير',
            'records_count' => 'عدد السجلات',
            'report_type' => 'نوع التقرير',
            'report_name' => 'اسم التقرير',
            'no_data' => 'لا توجد بيانات لعرضها',
            'total_records' => 'إجمالي السجلات',
        ];

        $pdf = Pdf::loadView('reports.pdf', [
            'report' => $report,
            'reportName' => $this->arabicText($report->name),
            'title' => $this->arabicText($this->getReportName($report->report_type)),
            'headers' => $pdfHeaders,
            'rows' => $pdfRows,
            'generatedAt' => $this->arabicText(now()->format('Y-m-d H:i')),
            'labels' => array_map(
                fn ($label) => $this->arabicText($label),
                $labels
            ),
        ]);

        $pdf->setPaper('A4', 'landscape');

        $path = 'reports/' . $report->id . '_' . now()->timestamp . '.pdf';

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    protected function generateExcel(
        Report $report,
        array $headers,
        array $rows
    ): string {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setRightToLeft(true);
        $sheet->setTitle('التقرير');

        $lastColumn = $this->columnLetter(count($headers));

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', $report->name);

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        foreach ($headers as $index => $header) {
            $column = $this->columnLetter($index + 1);
            $sheet->setCellValue($column . '3', $header);
        }

        $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
                'wrapText' => true,
            ],
        ]);

        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $column = $this->columnLetter($columnIndex + 1);

                $sheet->setCellValue(
                    $column . ($rowIndex + 4),
                    $value ?? '-'
                );
            }
        }

        $lastRow = count($rows) + 3;

        if ($lastRow >= 4) {
            $sheet->getStyle("A4:{$lastColumn}{$lastRow}")
                ->getAlignment()
                ->setHorizontal('center')
                ->setVertical('center')
                ->setWrapText(true);
        }

        for ($index = 1; $index <= count($headers); $index++) {
            $column = $this->columnLetter($index);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->freezePane('A4');

        $path = 'reports/' . $report->id . '_' . now()->timestamp . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        Storage::disk('local')->put($path, $content);

        $spreadsheet->disconnectWorksheets();

        return $path;
    }

    protected function columnLetter(int $column): string
    {
        $letter = '';

        while ($column > 0) {
            $remainder = ($column - 1) % 26;
            $letter = chr(65 + $remainder) . $letter;
            $column = (int) (($column - $remainder) / 26);
        }

        return $letter;
    }

    protected function getReportName(string $reportType): string
    {
        return match ($reportType) {
            'citizens' => 'تقرير المواطنين',
            'passports' => 'تقرير الجوازات',
            'identity_cards' => 'تقرير البطاقات الشخصية',
            'family_cards' => 'تقرير البطاقات العائلية',
            'birth_certificates' => 'تقرير شهادات الميلاد',
            'death_certificates' => 'تقرير شهادات الوفاة',
            'appointments' => 'تقرير المواعيد',
            default => 'تقرير النظام',
        };
    }
}
