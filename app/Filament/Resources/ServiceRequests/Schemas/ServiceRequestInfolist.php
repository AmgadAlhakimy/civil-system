<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الطلب')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('request_number')
                                ->label('رقم الطلب')
                                ->badge()
                                ->color('primary')
                                ->copyable()
                                ->fontFamily('mono'),

                            TextEntry::make('service_name')
                                ->label('الخدمة')
                                ->weight('bold'),

                            TextEntry::make('category_label')
                                ->label('الفئة')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('title')
                                ->label('العنوان')
                                ->columnSpanFull(),

                            TextEntry::make('formatted_amount')
                                ->label('المبلغ')
                                ->weight('bold')
                                ->color('success')
                                ->icon('heroicon-o-banknotes'),

                            TextEntry::make('payment_status_label')
                                ->label('حالة الدفع')
                                ->badge(),

                            TextEntry::make('status_label')
                                ->label('حالة الطلب')
                                ->badge(),

                            TextEntry::make('branch.name')
                                ->label('الفرع')
                                ->placeholder('غير محدد'),

                            TextEntry::make('user.name')
                                ->label('صاحب الطلب')
                                ->icon('heroicon-o-user'),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('البيانات المُدخلة')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        KeyValueEntry::make('data')
                            ->label('')
                            ->state(fn ($record) => self::formatData($record->data))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('المستندات المرفوعة')
                    ->icon('heroicon-o-folder-open')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('documents')
                            ->label('')
                            ->state(fn ($record) => self::formatDocumentsAsHtml(
                                self::collectDocuments(
                                    $record->documents,
                                    $record->data
                                )
                            ))
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('المراجعة')
                    ->icon('heroicon-o-check-badge')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('reviewer.name')
                                ->label('المراجع')
                                ->placeholder('لم تتم المراجعة'),

                            TextEntry::make('reviewed_at')
                                ->label('تاريخ المراجعة')
                                ->dateTime('Y/m/d H:i')
                                ->placeholder('لم تتم المراجعة'),

                            TextEntry::make('rejection_reason')
                                ->label('سبب الرفض')
                                ->placeholder('لا يوجد')
                                ->color('danger')
                                ->columnSpanFull(),

                            TextEntry::make('created_at')
                                ->label('تاريخ الإنشاء')
                                ->dateTime('Y/m/d H:i'),

                            TextEntry::make('updated_at')
                                ->label('آخر تحديث')
                                ->dateTime('Y/m/d H:i'),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * تحويل بيانات الطلب إلى قيم جاهزة للعرض.
     */
    protected static function formatData($data): array
    {
        if (! is_array($data)) {
            if (is_string($data) && ! empty($data)) {
                $decoded = json_decode($data, true);
                $data = is_array($decoded) ? $decoded : [];
            } else {
                $data = [];
            }
        }

        $labels = [
            'fullName' => 'الاسم الكامل',
            'firstName' => 'الاسم الأول',
            'fatherName' => 'اسم الأب',
            'grandfatherName' => 'اسم الجد',
            'familyName' => 'اللقب',
            'cardNumber' => 'رقم البطاقة',
            'nationalId' => 'الرقم الوطني',
            'phone' => 'رقم الهاتف',
            'email' => 'البريد الإلكتروني',
            'gender' => 'الجنس',
            'birthDate' => 'تاريخ الميلاد',
            'maritalStatus' => 'الحالة الاجتماعية',
            'area' => 'المنطقة/العزلة',
            'village' => 'القرية/الحارة',
            'addressDetails' => 'تفاصيل العنوان',
            'residenceGovernorateName' => 'محافظة الإقامة',
            'residenceDistrictName' => 'مديرية الإقامة',
            'residenceArea' => 'المنطقة/العزلة',
            'residenceVillage' => 'القرية/الحارة',
            'residenceAreaName' => 'المنطقة/العزلة',
            'residenceVillageName' => 'القرية/الحارة',
            'branchName' => 'الفرع',
            'birthGovernorateName' => 'محافظة الميلاد',
            'birthDistrictName' => 'مديرية الميلاد',
            'birthArea' => 'عزلة الميلاد',
            'birthVillage' => 'قرية الميلاد',
            'reason' => 'سبب التجديد',
            'job' => 'المهنة',
            'notes' => 'ملاحظات',
            'deceasedName' => 'اسم المتوفى',
            'deathDate' => 'تاريخ الوفاة',
            'deathPlace' => 'مكان الوفاة',
            'deathCase' => 'حالة الوفاة',
            'applicantName' => 'اسم مقدم الطلب',
            'relationship' => 'صلة القرابة',
            'newbornFirstName' => 'اسم المولود الأول',
            'newbornFatherName' => 'اسم الأب',
            'newbornGrandfatherName' => 'اسم الجد',
            'newbornFamilyName' => 'اللقب',
            'familyMembers' => 'أفراد الأسرة',
            'isRenewal' => 'تجديد',
        ];

        $exclude = [
            'documents',
            'transactionId',
            'paymentPhone',
            'birthGovernorateId',
            'birthDistrictId',
            'residenceGovernorateId',
            'residenceDistrictId',
            'branchId',
            'currentIdCardFront',
            'currentIdCardBack',
            'personalPhoto',
            'personal_photo',
            'personalPhotoOne',
            'personalPhotoTwo',
            'transaction_id',
        ];

        $result = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $exclude, true)) {
                continue;
            }

            if (! array_key_exists($key, $labels)) {
                continue;
            }

            $label = $labels[$key];

            if (
                str_contains(strtolower((string) $key), 'photo') ||
                str_contains(strtolower((string) $key), 'front') ||
                str_contains(strtolower((string) $key), 'back') ||
                str_contains(strtolower((string) $key), 'document')
            ) {
                continue;
            }

            if (
                str_contains(strtolower((string) $key), 'date') &&
                is_string($value) &&
                ! empty($value)
            ) {
                try {
                    $date = \Carbon\Carbon::parse($value);
                    $value = $date->format('Y/m/d');
                } catch (\Exception $e) {
                    // الاحتفاظ بالقيمة الأصلية إذا تعذر تحليل التاريخ.
                }
            }

            if (is_array($value)) {
                if ($key === 'familyMembers') {
                    $members = [];

                    foreach ($value as $i => $member) {
                        if (is_array($member)) {
                            $name = $member['fullName'] ?? 'فرد';
                            $relation = $member['relation'] ?? '';

                            $members[] = 'فرد ' . ($i + 1) . ": {$name} ({$relation})";
                        } else {
                            $members[] = 'فرد ' . ($i + 1) . ': ' . (string) $member;
                        }
                    }

                    $result[$label] = implode("\n", $members);
                }

                continue;
            }

            if (is_bool($value)) {
                $result[$label] = $value ? 'نعم' : 'لا';
            } else {
                $val = (string) ($value ?? '—');
                $result[$label] = $val === '' ? '—' : $val;
            }
        }

        return empty($result)
            ? ['—' => 'لا توجد بيانات']
            : $result;
    }

    /**
     * جمع المستندات من عمودي documents و data.
     */
    protected static function collectDocuments($documents, $data): array
    {
        if (! is_array($documents)) {
            $decoded = is_string($documents)
                ? json_decode($documents, true)
                : null;

            $documents = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($data)) {
            $decoded = is_string($data)
                ? json_decode($data, true)
                : null;

            $data = is_array($decoded) ? $decoded : [];
        }

        $documentKeys = [
            'currentIdCardFront',
            'currentIdCardBack',
            'personalPhoto',
            'personal_photo',
            'personalPhotoOne',
            'personalPhotoTwo',
            'personal_photo_one',
            'personal_photo_two',
            'proof_birth_certificate',
            'proof_seat_number',
            'proof_secondary_certificate',
            'father_card_front',
            'father_card_back',
            'witness_one_front',
            'witness_one_back',
            'witness_two_front',
            'witness_two_back',
            'birth_report',
            'hospital_document',
            'death_report',
            'deceased_id_front',
            'deceased_id_back',
            'baby_photo',
            'currentCard',
            'marriage',
            'marriage_document',
            'residence_proof',
            'passport',
            'supportingDocument',
        ];

        foreach ($documentKeys as $key) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            $document = $data[$key];

            if (is_string($document)) {
                $decoded = json_decode($document, true);

                if (is_array($decoded)) {
                    $document = $decoded;
                }
            }

            if (
                (is_array($document) &&
                    (! empty($document['url']) || ! empty($document['path']))) ||
                (is_string($document) &&
                    filter_var($document, FILTER_VALIDATE_URL))
            ) {
                $documents[$key] = $document;
            }
        }

        return $documents;
    }

    /**
     * عرض المستندات والصور في HTML.
     */
    protected static function formatDocumentsAsHtml($documents): string
    {
        if (! is_array($documents)) {
            if (is_string($documents) && ! empty($documents)) {
                $decoded = json_decode($documents, true);
                $documents = is_array($decoded) ? $decoded : [];
            } else {
                $documents = [];
            }
        }

        if (empty($documents)) {
            return '<p style="color: #6b7280;">لا توجد مستندات</p>';
        }

        $labels = [
            'proof_birth_certificate' => 'شهادة الميلاد',
            'proof_seat_number' => 'رقم الجلوس',
            'proof_secondary_certificate' => 'شهادة الثانوية',
            'birth_report' => 'تقرير الولادة',
            'hospital_document' => 'إثبات المستشفى',
            'death_report' => 'تقرير الوفاة',
            'father_card_front' => 'بطاقة الأب (أمامي)',
            'father_card_back' => 'بطاقة الأب (خلفي)',
            'father_front' => 'هوية الأب (أمامي)',
            'father_back' => 'هوية الأب (خلفي)',
            'mother_front' => 'هوية الأم (أمامي)',
            'mother_back' => 'هوية الأم (خلفي)',
            'deceased_id_front' => 'هوية المتوفى (أمامي)',
            'deceased_id_back' => 'هوية المتوفى (خلفي)',
            'currentIdCardFront' => 'البطاقة الحالية (أمامي)',
            'currentIdCardBack' => 'البطاقة الحالية (خلفي)',
            'witness_one_front' => 'الشاهد الأول (أمامي)',
            'witness_one_back' => 'الشاهد الأول (خلفي)',
            'witness_two_front' => 'الشاهد الثاني (أمامي)',
            'witness_two_back' => 'الشاهد الثاني (خلفي)',
            'personal_photo_one' => 'الصورة الشخصية 1',
            'personal_photo_two' => 'الصورة الشخصية 2',
            'personal_photo' => 'الصورة الشخصية',
            'personalPhoto' => 'الصورة الشخصية',
            'personalPhotoOne' => 'الصورة الشخصية 1',
            'personalPhotoTwo' => 'الصورة الشخصية 2',
            'baby_photo' => 'صورة المولود',
            'currentCard' => 'البطاقة العائلية الحالية',
            'marriage' => 'وثيقة الزواج',
            'marriage_document' => 'وثيقة الزواج',
            'marriage_divorce' => 'وثيقة الزواج/الطلاق',
            'headId' => 'هوية رب الأسرة',
            'residence_proof' => 'إثبات الإقامة',
            'birth_cert_front' => 'شهادة الميلاد (أمامي)',
            'birth_cert_back' => 'شهادة الميلاد (خلفي)',
            'passport' => 'جواز السفر',
            'supportingDocument' => 'مستند داعم',
            'special_case_report' => 'تقرير خاص',
        ];

        $html = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">';

        foreach ($documents as $key => $doc) {
            $label = $labels[$key] ?? 'مستند مرفق';
            $url = null;

            if (is_array($doc)) {
                $url = $doc['url'] ?? $doc['path'] ?? null;
            } elseif (is_string($doc) && ! empty($doc)) {
                $url = $doc;
            }

            if (! is_string($url) || $url === '') {
                continue;
            }

            $safeUrl = e($url);

            $html .= '<div style="border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
            $html .= '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" style="display: block;">';
            $html .= '<img src="' . $safeUrl . '" alt="' . e($label) . '" style="width: 100%; height: 180px; object-fit: cover; display: block;">';
            $html .= '</a>';
            $html .= '<div style="padding: 8px; text-align: center; font-size: 12px; font-weight: 600; color: #374151; background: #f9fafb;">' . e($label) . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}
