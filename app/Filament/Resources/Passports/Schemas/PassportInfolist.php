<?php

namespace App\Filament\Resources\Passports\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PassportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات الجواز')
                    ->description('المعلومات الأساسية لجواز السفر')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 4,
                        ])
                            ->schema([

                                Section::make()
                                    ->schema([
                                        ImageEntry::make('citizen.photo')
                                            ->label('الصورة الشخصية')
                                            ->getStateUsing(
                                                fn ($record) => $record->citizen?->photo
                                                    ? route(
                                                        'citizens.photo',
                                                        [
                                                            'path' => basename($record->citizen->photo),
                                                        ]
                                                    )
                                                    : null
                                            )
                                            ->circular()
                                            ->imageSize(170)
                                            ->defaultImageUrl(url('/images/default-avatar.png'))
                                            ->extraImgAttributes([
                                                'class' => 'object-cover shadow-lg ring-4 ring-white dark:ring-gray-800',
                                            ])
                                            ->alignCenter(),
                                    ])
                                    ->columnSpan(1),

                                Grid::make(2)
                                    ->schema([

                                        TextEntry::make('passport_number')
                                            ->label('رقم الجواز')
                                            ->weight('bold'),

                                        TextEntry::make('citizen')
                                            ->label('المواطن')
                                            ->formatStateUsing(
                                                fn ($record): string => collect([
                                                    $record->citizen?->first_name,
                                                    $record->citizen?->father_name,
                                                    $record->citizen?->middle_name,
                                                    $record->citizen?->last_name,
                                                ])
                                                    ->filter()
                                                    ->join(' ')
                                            ),

                                        TextEntry::make('type')
                                            ->label('نوع الجواز')
                                            ->formatStateUsing(
                                                fn (?string $state): string => match ($state) {
                                                    'ordinary' => 'عادي',
                                                    'diplomatic' => 'دبلوماسي',
                                                    'official' => 'رسمي',
                                                    default => 'غير محدد',
                                                }
                                            ),

                                        TextEntry::make('status')
                                            ->label('حالة الجواز')
                                            ->formatStateUsing(
                                                fn (?string $state): string => match ($state) {
                                                    'pending' => 'قيد الانتظار',
                                                    'rejected' => 'مرفوض',
                                                    'active' => 'ساري',
                                                    'expired' => 'منتهي',
                                                    'cancelled' => 'ملغي',
                                                    'lost' => 'مفقود',
                                                    'damaged' => 'تالف',
                                                    default => 'غير محدد',
                                                }
                                            ),

                                        TextEntry::make('issue_date')
                                            ->label('تاريخ الإصدار')
                                            ->date('d/m/Y')
                                            ->placeholder('لم يتم الإصدار بعد'),

                                        TextEntry::make('expiry_date')
                                            ->label('تاريخ الانتهاء')
                                            ->date('d/m/Y')
                                            ->placeholder('لم يتم تحديده بعد'),

                                    ])
                                    ->columnSpan(3),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات الإصدار والاعتماد')
                    ->description('المعلومات النظامية المتعلقة بإنشاء واعتماد الجواز')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('issuedBy.name')
                                    ->label('تم إنشاء الطلب بواسطة')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('approvedBy.name')
                                    ->label('تم الاعتماد بواسطة')
                                    ->placeholder('لم يتم الاعتماد بعد'),

                                TextEntry::make('approved_at')
                                    ->label('تاريخ ووقت الاعتماد')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('لم يتم الاعتماد بعد'),

                                TextEntry::make('print_count')
                                    ->label('عدد مرات الطباعة')
                                    ->numeric(),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('الملاحظات وسبب الرفض والبيانات الإضافية المرتبطة بالجواز')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('notes')
                                    ->label('ملاحظات')
                                    ->placeholder('لا توجد ملاحظات')
                                    ->columnSpan(1),

                                TextEntry::make('rejection_reason')
                                    ->label('سبب الرفض')
                                    ->icon('heroicon-o-exclamation-circle')
                                    ->color('danger')
                                    ->placeholder('لم يتم تسجيل سبب الرفض')
                                    ->columnSpan(1)
                                    ->visible(
                                        fn ($record): bool => $record?->status === 'rejected'
                                    ),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث سجل الجواز')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('created_at')
                                    ->label('تاريخ التسجيل')
                                    ->dateTime('d/m/Y H:i'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime('d/m/Y H:i'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('التحقق الإلكتروني')
                    ->description('امسح رمز QR لفتح صفحة التحقق من حالة الجواز')
                    ->icon('heroicon-o-qr-code')
                    ->schema([
                        ViewEntry::make('verification_qr')
                            ->hiddenLabel()
                            ->view('filament.infolists.passport-verification-qr')
                            ->state(
                                fn ($record): array => [
                                    'url' => route(
                                        'passports.verify',
                                        ['passport' => $record->getKey()]
                                    ),
                                ]
                            )
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
