<?php

namespace App\Filament\Resources\Reports\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم التقرير')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('report_type')
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
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('format')
                    ->label('الصيغة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pdf' => 'PDF',
                            'xlsx', 'excel' => 'Excel',
                            default => 'غير محدد',
                        }
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('أنشأه')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'قيد الانتظار',
                            'completed' => 'مكتمل',
                            'failed' => 'فشل',
                            default => 'غير محدد',
                        }
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'warning',
                            'completed' => 'success',
                            'failed' => 'danger',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('size')
                    ->label('حجم الملف')
                    ->formatStateUsing(function ($state): string {
                        if (!$state) {
                            return 'غير متوفر';
                        }

                        if ($state < 1024) {
                            return $state . ' بايت';
                        }

                        if ($state < 1024 * 1024) {
                            return round($state / 1024, 2) . ' KB';
                        }

                        if ($state < 1024 * 1024 * 1024) {
                            return round($state / (1024 * 1024), 2) . ' MB';
                        }

                        return round($state / (1024 * 1024 * 1024), 2) . ' GB';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('generated_at')
                    ->label('تاريخ التوليد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('لم يتم التوليد')
                    ->toggleable(),

                TextColumn::make('path')
                    ->label('مسار الملف')
                    ->limit(40)
                    ->placeholder('لا يوجد ملف')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('report_type')
                    ->label('نوع التقرير')
                    ->options([
                        'citizens' => 'تقرير المواطنين',
                        'birth_certificates' => 'تقرير شهادات الميلاد',
                        'identity_cards' => 'تقرير البطاقات الشخصية',
                        'family_cards' => 'تقرير البطاقات العائلية',
                        'passports' => 'تقرير الجوازات',
                        'appointments' => 'تقرير المواعيد',
                    ]),

                SelectFilter::make('format')
                    ->label('صيغة التقرير')
                    ->options([
                        'pdf' => 'PDF',
                        'xlsx' => 'Excel',
                        'excel' => 'Excel',
                    ]),

                SelectFilter::make('status')
                    ->label('حالة التقرير')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'completed' => 'مكتمل',
                        'failed' => 'فشل',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),

                Action::make('download')
                    ->label('تحميل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(
                        fn ($record): bool =>
                            $record->status === 'completed'
                            && filled($record->path)
                            && Storage::disk('local')->exists($record->path)
                    )
                    ->action(function ($record) {
                        return Storage::disk('local')->download(
                            $record->path,
                            basename($record->path)
                        );
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('حذف التقارير المحددة')
                        ->modalDescription('هل أنت متأكد من حذف التقارير المحددة؟')
                        ->modalSubmitActionLabel('نعم، حذف'),
                ])
                    ->label('إجراءات جماعية'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
