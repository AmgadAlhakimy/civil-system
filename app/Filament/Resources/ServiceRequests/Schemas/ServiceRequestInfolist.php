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
                // ═══════════════════════════════════════════════
                // معلومات الطلب
                // ═══════════════════════════════════════════════
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
                    ])->columnSpanFull(),

                // ═══════════════════════════════════════════════
                // ✅ البيانات المُدخلة — بدون UUIDs
                // ═══════════════════════════════════════════════
                Section::make('البيانات المُدخلة')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        KeyValueEntry::make('data_display')
                            ->label('')
                            ->state(fn ($record) => self::formatData($record->data))
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // ═══════════════════════════════════════════════
                // ✅ المستندات — عرض الصور عبر Markdown
                // ═══════════════════════════════════════════════
                Section::make('المستندات المرفوعة')
                    ->icon('heroicon-o-folder-open')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('documents_html')
                            ->label('')
                            ->state(fn ($record) => self::formatDocumentsAsHtml($record->documents))
                            ->html()
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // ═══════════════════════════════════════════════
                // المراجعة
                // ═══════════════════════════════════════════════
                Section::make('المراجعة')
                    ->icon('heroicon-o-check-badge')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('reviewedBy.name')
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
                    ])->columnSpanFull(),
            ]);
    }

    // ═══════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════

    /**
     * تحويل data إلى array جاهز للعرض
     */
    protected static function formatData($data): array
    {
        if (!is_array($data)) {
            if (is_string($data) && !empty($data)) {
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
            'residenceArea' => 'عزلة الإقامة',
            'residenceVillage' => 'قرية الإقامة',
            'birthGovernorateName' => 'محافظة الميلاد',
            'birthDistrictName' => 'مديرية الميلاد',
            'birthArea' => 'عزلة الميلاد',
            'birthVillage' => 'قرية الميلاد',
            'branchName' => 'الفرع',
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
        ];

        $result = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $exclude, true)) continue;

            $label = $labels[$key] ?? $key;

            // ✅ معالجة التواريخ
            if (str_contains(strtolower($key), 'date') && is_string($value) && !empty($value)) {
                try {
                    $date = \Carbon\Carbon::parse($value);
                    $value = $date->format('Y/m/d');
                } catch (\Exception $e) {
                    // اترك القيمة كما هي
                }
            }

            if (is_array($value)) {
                if ($key === 'familyMembers') {
                    $members = [];
                    foreach ($value as $i => $member) {
                        if (is_array($member)) {
                            $name = $member['fullName'] ?? 'فرد';
                            $relation = $member['relation'] ?? '';
                            $members[] = "فرد " . ($i + 1) . ": {$name} ({$relation})";
                        } else {
                            $members[] = "فرد " . ($i + 1) . ": " . (string) $member;
                        }
                    }
                    $result[$label] = implode("\n", $members);
                } else {
                    $result[$label] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
            } elseif (is_bool($value)) {
                $result[$label] = $value ? 'نعم' : 'لا';
            } else {
                $val = (string) ($value ?? '—');
                $result[$label] = $val === '' ? '—' : $val;
            }
        }

        return empty($result) ? ['—' => 'لا توجد بيانات'] : $result;
    }

    /**
     * ✅ تحويل documents إلى HTML يعرض الصور مباشرة
     */
    protected static function formatDocumentsAsHtml($documents): string
    {
        if (!is_array($documents)) {
            if (is_string($documents) && !empty($documents)) {
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
            $label = $labels[$key] ?? $key;
            $url = null;

            if (is_array($doc) && isset($doc['url'])) {
                $url = $doc['url'];
            } elseif (is_string($doc) && !empty($doc)) {
                $url = $doc;
            }

            if (!$url) continue;

            $html .= '<div style="border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
            $html .= '<a href="' . e($url) . '" target="_blank" style="display: block;">';
            $html .= '<img src="' . e($url) . '" alt="' . e($label) . '" style="width: 100%; height: 180px; object-fit: cover; display: block;" onerror="this.style.display=\'none\'; this.parentElement.innerHTML=\'<div style=&quot;height:180px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:12px;&quot;>تعذر تحميل الصورة</div>\';">';
            $html .= '</a>';
            $html .= '<div style="padding: 8px; text-align: center; font-size: 12px; font-weight: 600; color: #374151; background: #f9fafb;">' . e($label) . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}