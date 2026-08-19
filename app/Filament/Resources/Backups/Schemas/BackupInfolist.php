<?php

namespace App\Filament\Resources\Backups\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BackupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات النسخة الاحتياطية')
                    ->description('المعلومات الأساسية للنسخة الاحتياطية')
                    ->icon('heroicon-o-archive-box')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('اسم النسخة')
                                    ->copyable(),

                                TextEntry::make('type')
                                    ->label('نوع النسخة')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'manual' => 'يدوية',
                                            'automatic' => 'تلقائية',
                                            default => $state ?? 'غير محدد',
                                        }
                                    )
                                    ->badge(),

                                TextEntry::make('status')
                                    ->label('الحالة')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'pending' => 'قيد التنفيذ',
                                            'completed' => 'مكتملة',
                                            'failed' => 'فشلت',
                                            default => $state ?? 'غير محدد',
                                        }
                                    )
                                    ->badge(),

                                TextEntry::make('file_size')
                                    ->label('حجم النسخة')
                                    ->formatStateUsing(function ($state): string {
                                        if (! $state) {
                                            return '-';
                                        }

                                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                        $size = (float) $state;
                                        $unit = 0;

                                        while ($size >= 1024 && $unit < count($units) - 1) {
                                            $size /= 1024;
                                            $unit++;
                                        }

                                        return number_format($size, 2) . ' ' . $units[$unit];
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('ملف النسخة')
                    ->description('موقع ملف النسخة الاحتياطية')
                    ->icon('heroicon-o-archive-box')
                    ->schema([
                        TextEntry::make('file_path')
                            ->label('مسار الملف')
                            ->copyable()
                            ->placeholder('غير متوفر')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات الإنشاء')
                    ->description('معلومات المستخدم والتواريخ المرتبطة بالنسخة')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('createdBy.name')
                                    ->label('أنشأ النسخة')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('created_at')
                                    ->label('تاريخ الإنشاء')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('completed_at')
                                    ->label('تاريخ الإكمال')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('لم تكتمل'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('ملاحظات')
                    ->description('الملاحظات المرتبطة بالنسخة')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('الملاحظات')
                            ->placeholder('لا توجد ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
