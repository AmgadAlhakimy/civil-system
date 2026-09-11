<?php

namespace App\Filament\Resources\Backups\Tables;

use App\Models\Backup;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BackupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم النسخة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('نوع النسخة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'manual' => 'يدوية',
                            'automatic' => 'تلقائية',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'قيد التنفيذ',
                            'completed' => 'مكتملة',
                            'failed' => 'فشلت',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('file_size')
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
                    })
                    ->sortable(),

                TextColumn::make('createdBy.name')
                    ->label('أنشأها')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد'),

                TextColumn::make('completed_at')
                    ->label('تاريخ الإكمال')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('لم تكتمل'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع النسخة')
                    ->options([
                        'manual' => 'يدوية',
                        'automatic' => 'تلقائية',
                    ]),

                SelectFilter::make('status')
                    ->label('حالة النسخة')
                    ->options([
                        'pending' => 'قيد التنفيذ',
                        'completed' => 'مكتملة',
                        'failed' => 'فشلت',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye'),

                Action::make('download')
                    ->label('تحميل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(
                        fn (Backup $record): bool =>
                            $record->status === 'completed' &&
                            filled($record->file_path) &&
                            Storage::disk('local')->exists($record->file_path)
                    )
                    ->action(function (Backup $record) {
                        return Storage::disk('local')->download(
                            $record->file_path,
                            basename($record->file_path)
                        );
                    }),

                Action::make('delete')
                    ->label('حذف')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('حذف النسخة الاحتياطية')
                    ->modalDescription(
                        'هل أنت متأكد من حذف هذه النسخة الاحتياطية؟ سيتم حذف ملف النسخة وسجلها من النظام.'
                    )
                    ->modalSubmitActionLabel('نعم، حذف')
                    ->modalCancelActionLabel('إلغاء')
                    ->action(function (Backup $record): void {
                        $disk = Storage::disk('local');

                        if (
                            filled($record->file_path) &&
                            $disk->exists($record->file_path)
                        ) {
                            $disk->delete($record->file_path);
                        }

                        $record->delete();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('حذف المحدد')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('حذف النسخ الاحتياطية')
                        ->modalDescription(
                            'هل أنت متأكد من حذف النسخ الاحتياطية المحددة؟ سيتم حذف ملفات النسخ وسجلاتها من النظام.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->modalCancelActionLabel('إلغاء')
                        ->action(function ($records): void {
                            $disk = Storage::disk('local');

                            foreach ($records as $record) {
                                if (
                                    filled($record->file_path) &&
                                    $disk->exists($record->file_path)
                                ) {
                                    $disk->delete($record->file_path);
                                }

                                $record->delete();
                            }
                        }),
                ])
                    ->label('إجراءات جماعية'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
