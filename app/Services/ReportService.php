<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Appointment;
use App\Models\BirthCertificate;
use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\IdentityCard;
use App\Models\Passport;
use Barryvdh\DomPDF\Facade\Pdf;
use ArPHP\I18N\Arabic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class ReportService
{
    public function generate(
        string $reportType,
        string $format,
        array $filters = []
    ): Report {
        $report = Report::create([
            'name' => $this->getReportName($reportType),
            'report_type' => $reportType,
            'format' => $format,
            'user_id' => Auth::id(),
            'filters' => $filters ?: null,
            'status' => 'pending',
        ]);

        try {
            $data = $this->getData($reportType, $filters);

            if ($format === 'pdf') {
                $path = $this->generatePdf($report, $data);
            } elseif ($format === 'xlsx') {
                $path = $this->generateExcel($report, $data);
            } else {
                throw new \InvalidArgumentException(
                    'صيغة التقرير غير مدعومة.'
                );
            }

            $size = Storage::disk('local')->size($path);

            $report->update([
                'path' => $path,
                'size' => $size,
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            return $report->fresh();

        } catch (Throwable $e) {
            $report->update([
                'status' => 'failed',
            ]);

            throw $e;
        }
    }

    protected function getData(
        string $reportType,
        array $filters
    ): array {
        return match ($reportType) {
            'citizens' => [
                'title' => 'تقرير المواطنين',
                'headers' => [
                    'الرقم الوطني',
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
                'rows' => $this->citizens($filters),
            ],

            'passports' => [
                'title' => 'تقرير جوازات السفر',
                'headers' => [
                    'رقم الجواز',
                    'الرقم الوطني',
                    'اسم المواطن',
                    'نوع الجواز',
                    'تاريخ الإصدار',
                    'تاريخ الانتهاء',
                    'الحالة',
                ],
                'rows' => $this->passports($filters),
            ],

            'identity_cards' => [
                'title' => 'تقرير البطاقات الشخصية',
                'headers' => [
                    'رقم البطاقة',
                    'الرقم الوطني',
                    'اسم المواطن',
                    'تاريخ الإصدار',
                    'تاريخ الانتهاء',
                    'الحالة',
                ],
                'rows' => $this->identityCards($filters),
            ],

            'family_cards' => [
                'title' => 'تقرير البطاقات العائلية',
                'headers' => [
                    'رقم البطاقة',
                    'الرقم الوطني لرب الأسرة',
                    'اسم رب الأسرة',
                    'تاريخ الإصدار',
                    'تاريخ الانتهاء',
                    'الحالة',
                ],
                'rows' => $this->familyCards($filters),
            ],

            'birth_certificates' => [
                'title' => 'تقرير شهادات الميلاد',
                'headers' => [
                    'رقم الشهادة',
                    'اسم الطفل',
                    'الرقم الوطني للطفل',
                    'اسم الأب',
                    'اسم الأم',
                    'تاريخ الإصدار',
                    'الحالة',
                ],
                'rows' => $this->birthCertificates($filters),
            ],

            'appointments' => [
                'title' => 'تقرير المواعيد',
                'headers' => [
                    'المواطن',
                    'الرقم الوطني',
                    'نوع الخدمة',
                    'الفرع',
                    'الموظف',
                    'تاريخ الموعد',
                    'وقت الموعد',
                    'الحالة',
                ],
                'rows' => $this->appointments($filters),
            ],

            default => throw new \InvalidArgumentException(
                'نوع التقرير غير معروف.'
            ),
        };
    }

    protected function citizens(array $filters): array
    {
        $query = Citizen::query();

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['marital_status'])) {
            $query->where(
                'marital_status',
                $filters['marital_status']
            );
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where(
                'is_active',
                (bool) $filters['is_active']
            );
        }

        return $query
            ->orderBy('first_name')
            ->get()
            ->map(function ($citizen) {
                return [
                    $citizen->national_id,
                    $citizen->full_name,
                    $citizen->father_name,
                    $citizen->mother_name,
                    optional($citizen->birth_date)->format('Y-m-d'),
                    $citizen->birth_place,
                    $this->gender($citizen->gender),
                    $this->maritalStatus($citizen->marital_status),
                    $citizen->occupation ?? '-',
                    $citizen->phone,
                    $citizen->address,
                    $citizen->is_active ? 'نشط' : 'غير نشط',
                ];
            })
            ->toArray();
    }

    protected function passports(array $filters): array
    {
        $query = Passport::query()->with('citizen');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query
            ->latest()
            ->get()
            ->map(function ($passport) {
                return [
                    $passport->passport_number,
                    $passport->citizen?->national_id ?? '-',
                    $passport->citizen?->full_name ?? '-',
                    $this->passportType($passport->type),
                    $passport->issue_date?->format('Y-m-d'),
                    $passport->expiry_date?->format('Y-m-d'),
                    $this->passportStatus($passport->status),
                ];
            })
            ->toArray();
    }

    protected function identityCards(array $filters): array
    {
        $query = IdentityCard::query()->with('citizen');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest()
            ->get()
            ->map(function ($card) {
                return [
                    $card->id_number,
                    $card->citizen?->national_id ?? '-',
                    $card->citizen?->full_name ?? '-',
                    $card->issue_date?->format('Y-m-d'),
                    $card->expiry_date?->format('Y-m-d'),
                    $this->identityCardStatus($card->status),
                ];
            })
            ->toArray();
    }

    protected function familyCards(array $filters): array
    {
        $query = FamilyCard::query()->with('head');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest()
            ->get()
            ->map(function ($card) {
                return [
                    $card->card_number,
                    $card->head?->national_id ?? '-',
                    $card->head?->full_name ?? '-',
                    $card->issue_date?->format('Y-m-d'),
                    $card->expiry_date?->format('Y-m-d'),
                    $this->familyCardStatus($card->status),
                ];
            })
            ->toArray();
    }

    protected function birthCertificates(array $filters): array
    {
        $query = BirthCertificate::query()->with([
            'child',
            'father',
            'mother',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest()
            ->get()
            ->map(function ($certificate) {
                return [
                    $certificate->certificate_number,
                    $certificate->child?->full_name ?? '-',
                    $certificate->child?->national_id ?? '-',
                    $certificate->father?->full_name ?? '-',
                    $certificate->mother?->full_name ?? '-',
                    $certificate->issue_date?->format('Y-m-d'),
                    $this->certificateStatus($certificate->status),
                ];
            })
            ->toArray();
    }

    protected function appointments(array $filters): array
    {
        $query = Appointment::query()->with([
            'citizen',
            'branch',
            'user',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        return $query
            ->orderByDesc('appointment_date')
            ->get()
            ->map(function ($appointment) {
                return [
                    $appointment->citizen?->full_name ?? '-',
                    $appointment->citizen?->national_id ?? '-',
                    $this->serviceType($appointment->service_type),
                    $appointment->branch?->name ?? '-',
                    $appointment->user?->name ?? '-',
                    $appointment->appointment_date?->format('Y-m-d'),
                    $appointment->appointment_time,
                    $this->appointmentStatus($appointment->status),
                ];
            })
            ->toArray();
    }

    protected function arabicText(mixed $text): string
    {
        if ($text === null || $text === '') {
            return '-';
        }

        $text = (string) $text;

        if (!preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return $text;
        }

        $arabic = new Arabic();

        return $arabic->utf8Glyphs($text);
    }

    protected function generatePdf(
        Report $report,
        array $data
    ): string {
        $filename =
            'reports/' .
            $report->id .
            '_' .
            now()->format('Ymd_His') .
            '.pdf';

        $headers = array_map(
            fn ($header) => $this->arabicText($header),
            $data['headers']
        );

        $rows = array_map(
            function ($row) {
                return array_map(
                    fn ($value) => $this->arabicText($value),
                    $row
                );
            },
            $data['rows']
        );

        $title = $this->arabicText($data['title']);

        $reportName = $this->arabicText($report->name);

        /*
         * النصوص الثابتة في التقرير
         */
        $labels = [
            'report_date' => $this->arabicText('تاريخ إنشاء التقرير'),
            'report_name' => $this->arabicText('اسم التقرير'),
            'report_type' => $this->arabicText('نوع التقرير'),
            'records_count' => $this->arabicText('عدد السجلات'),
            'format' => $this->arabicText('الصيغة'),
            'total_records' => $this->arabicText('إجمالي السجلات'),
            'no_data' => $this->arabicText(
                'لا توجد بيانات مطابقة للمعايير المحددة'
            ),
        ];

        $pdf = Pdf::loadView(
            'reports.pdf',
            [
                'report' => $report,
                'reportName' => $reportName,
                'title' => $title,
                'headers' => $headers,
                'rows' => $rows,
                'labels' => $labels,
            ]
        );

        $pdf->setPaper('a4', 'landscape');

        Storage::disk('local')->put(
            $filename,
            $pdf->output()
        );

        return $filename;
    }

    protected function generateExcel(
        Report $report,
        array $data
    ): string {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('التقرير');

        $sheet->setRightToLeft(true);

        $sheet->setCellValue(
            'A1',
            $data['title']
        );

        $sheet->mergeCells(
            'A1:' .
            $this->columnLetter(
                count($data['headers'])
            ) .
            '1'
        );

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(16);

        $headerRow = 3;

        foreach ($data['headers'] as $index => $header) {
            $column = $this->columnLetter($index + 1);

            $sheet->setCellValue(
                $column . $headerRow,
                $header
            );

            $sheet
                ->getStyle($column . $headerRow)
                ->getFont()
                ->setBold(true);
        }

        $rowNumber = 4;

        foreach ($data['rows'] as $row) {
            foreach ($row as $index => $value) {
                $column = $this->columnLetter($index + 1);

                $sheet->setCellValue(
                    $column . $rowNumber,
                    $value
                );
            }

            $rowNumber++;
        }

        foreach (
            range(
                1,
                count($data['headers'])
            ) as $columnIndex
        ) {
            $column = $this->columnLetter($columnIndex);

            $sheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $sheet->freezePane('A4');

        $filename =
            'reports/' .
            $report->id .
            '_' .
            now()->format('Ymd_His') .
            '.xlsx';

        $temporaryPath =
            storage_path(
                'app/private/' . $filename
            );

        if (!is_dir(dirname($temporaryPath))) {
            mkdir(
                dirname($temporaryPath),
                0755,
                true
            );
        }

        $writer = new Xlsx($spreadsheet);

        $writer->save($temporaryPath);

        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        return $filename;
    }

    protected function columnLetter(int $number): string
    {
        $letter = '';

        while ($number > 0) {
            $modulo = ($number - 1) % 26;

            $letter =
                chr(65 + $modulo) .
                $letter;

            $number =
                (int) (($number - $modulo) / 26) - 1;
        }

        return $letter;
    }

    protected function getReportName(
        string $type
    ): string {
        return match ($type) {
            'citizens' => 'تقرير المواطنين',
            'passports' => 'تقرير جوازات السفر',
            'identity_cards' => 'تقرير البطاقات الشخصية',
            'family_cards' => 'تقرير البطاقات العائلية',
            'birth_certificates' => 'تقرير شهادات الميلاد',
            'appointments' => 'تقرير المواعيد',
            default => 'تقرير',
        };
    }

    protected function gender(?string $value): string
    {
        return match ($value) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => 'غير محدد',
        };
    }

    protected function maritalStatus(?string $value): string
    {
        return match ($value) {
            'single' => 'أعزب',
            'married' => 'متزوج',
            'divorced' => 'مطلق',
            'widowed' => 'أرمل',
            default => 'غير محدد',
        };
    }

    protected function passportType(?string $value): string
    {
        return match ($value) {
            'ordinary' => 'عادي',
            'diplomatic' => 'دبلوماسي',
            'official' => 'رسمي',
            default => 'غير محدد',
        };
    }

    protected function passportStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'قيد الانتظار',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            'active' => 'نشط',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
            'lost' => 'مفقود',
            'damaged' => 'تالف',
            default => 'غير محدد',
        };
    }

    protected function identityCardStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'قيد الانتظار',
            'active' => 'نشطة',
            'expired' => 'منتهية',
            'cancelled' => 'ملغاة',
            'lost' => 'مفقودة',
            'damaged' => 'تالفة',
            default => 'غير محدد',
        };
    }

    protected function familyCardStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'قيد الانتظار',
            'active' => 'نشطة',
            'expired' => 'منتهية',
            'cancelled' => 'ملغاة',
            default => 'غير محدد',
        };
    }

    protected function certificateStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'قيد الانتظار',
            'approved' => 'معتمدة',
            'rejected' => 'مرفوضة',
            default => 'غير محدد',
        };
    }

    protected function appointmentStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'attended' => 'تم الحضور',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر',
            default => 'غير محدد',
        };
    }

    protected function serviceType(?string $value): string
    {
        return match ($value) {
            'passport_new' => 'إصدار جواز سفر',
            'passport_renew' => 'تجديد جواز سفر',
            'passport_lost' => 'بدل فاقد لجواز السفر',
            'passport_damaged' => 'بدل تالف لجواز السفر',
            'national_id_new' => 'إصدار بطاقة شخصية',
            'national_id_renew' => 'تجديد بطاقة شخصية',
            'national_id_lost' => 'بدل فاقد للبطاقة الشخصية',
            'national_id_damaged' => 'بدل تالف للبطاقة الشخصية',
            'family_card_new' => 'إصدار بطاقة عائلية',
            'family_card_renew' => 'تجديد بطاقة عائلية',
            'birth_certificate' => 'إصدار شهادة ميلاد',
            'death_certificate' => 'إصدار شهادة وفاة',
            default => 'غير محدد',
        };
    }
}
