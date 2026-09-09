<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات التقرير')
                    ->description('المعلومات الأساسية للتقرير')
                    ->icon('heroicon-o-document-chart-bar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('اسم التقرير')
                                    ->placeholder('غير محدد')
                                    ->weight('bold'),

                                TextEntry::make('report_type')
                                    ->label('نوع التقرير')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'citizens' => 'تقرير المواطنين',
                                            'birth_certificates' => 'تقرير شهادات الميلاد',
                                            'identity_cards' => 'تقرير البطاقات الشخصية',
                                            'family_cards' => 'تقرير البطاقات العائلية',
                                            'passports' => 'تقرير الجوازات',
                                            'appointments' => 'تقرير المواعيد',
                                            default => 'غير محدد',
                                        }
                                    ),

                                TextEntry::make('format')
                                    ->label('صيغة التقرير')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'pdf' => 'PDF',
                                            'excel' => 'Excel',
                                            default => 'غير محدد',
                                        }
                                    )
                                    ->color(
                                        fn (?string $state): string => match ($state) {
                                            'pdf' => 'danger',
                                            'excel' => 'success',
                                            default => 'gray',
                                        }
                                    ),

                                TextEntry::make('status')
                                    ->label('حالة التقرير')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'pending' => 'قيد الانتظار',
                                            'completed' => 'مكتمل',
                                            'failed' => 'فشل',
                                            default => 'غير محدد',
                                        }
                                    )
                                    ->color(
                                        fn (?string $state): string => match ($state) {
                                            'pending' => 'warning',
                                            'completed' => 'success',
                                            'failed' => 'danger',
                                            default => 'gray',
                                        }
                                    ),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('الفلاتر المستخدمة')
                    ->description('معايير البحث والفلترة التي تم استخدامها لإنشاء التقرير')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('filters.gender')
                                    ->label('الجنس')
                                    ->formatStateUsing(
                                        fn ($state): string => match ($state) {
                                            'male' => 'ذكر',
                                            'female' => 'أنثى',
                                            default => 'جميع المواطنين',
                                        }
                                    )
                                    ->placeholder('لم يتم تحديد فلتر'),

                                TextEntry::make('filters.marital_status')
                                    ->label('الحالة الاجتماعية')
                                    ->formatStateUsing(
                                        fn ($state): string => match ($state) {
                                            'single' => 'أعزب',
                                            'married' => 'متزوج',
                                            'divorced' => 'مطلق',
                                            'widowed' => 'أرمل',
                                            default => 'جميع الحالات',
                                        }
                                    )
                                    ->placeholder('لم يتم تحديد فلتر'),

                                TextEntry::make('filters.from_date')
                                    ->label('من تاريخ')
                                    ->date('d/m/Y')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('filters.to_date')
                                    ->label('إلى تاريخ')
                                    ->date('d/m/Y')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('filters.status')
                                    ->label('حالة الموعد')
                                    ->formatStateUsing(
                                        fn ($state): string => match ($state) {
                                            'pending' => 'قيد الانتظار',
                                            'confirmed' => 'مؤكد',
                                            'attended' => 'تم الحضور',
                                            'cancelled' => 'ملغي',
                                            'no_show' => 'لم يحضر',
                                            default => 'جميع الحالات',
                                        }
                                    )
                                    ->placeholder('لم يتم تحديد فلتر'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات الملف')
                    ->description('معلومات الملف الناتج عن التقرير')
                    ->icon('heroicon-o-document')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('path')
                                    ->label('مسار الملف')
                                    ->placeholder('لم يتم إنشاء الملف')
                                    ->copyable()
                                    ->copyMessage('تم نسخ مسار الملف')
                                    ->copyMessageDuration(1500)
                                    ->columnSpanFull(),

                                TextEntry::make('size')
                                    ->label('حجم الملف')
                                    ->formatStateUsing(function ($state): string {
                                        if (!$state) {
                                            return 'غير متوفر';
                                        }

                                        if ($state < 1024) {
                                            return number_format($state) . ' بايت';
                                        }

                                        if ($state < 1024 * 1024) {
                                            return number_format($state / 1024, 2) . ' KB';
                                        }

                                        if ($state < 1024 * 1024 * 1024) {
                                            return number_format(
                                                    $state / (1024 * 1024),
                                                    2
                                                ) . ' MB';
                                        }

                                        return number_format(
                                                $state / (1024 * 1024 * 1024),
                                                2
                                            ) . ' GB';
                                    })
                                    ->placeholder('غير متوفر'),

                                TextEntry::make('generated_at')
                                    ->label('تاريخ التوليد')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('لم يتم التوليد'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات المستخدم')
                    ->description('المستخدم الذي قام بإنشاء التقرير')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('أنشأ التقرير')
                            ->placeholder('غير محدد')
                            ->weight('bold'),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث سجل التقرير')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء التقرير')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
