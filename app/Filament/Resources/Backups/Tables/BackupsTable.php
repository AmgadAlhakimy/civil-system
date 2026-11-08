<?php

namespace App\Filament\Resources\Backups\Tables;

use App\Models\Backup;
use App\Services\BackupRestoreService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
                    ->sortable(),

                TextColumn::make('backup_scope')
                    ->label('نطاق النسخة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'database' => 'قاعدة البيانات فقط',
                            'full' => 'قاعدة البيانات وملفات النظام',
                            default => $state ?? 'غير محدد',
                        }
                    )
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

                TextColumn::make('createdBy.full_name')
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

                SelectFilter::make('backup_scope')
                    ->label('نطاق النسخة')
                    ->options([
                        'database' => 'قاعدة البيانات فقط',
                        'full' => 'قاعدة البيانات وملفات النظام',
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
                    ->icon('heroicon-o-eye')
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->can('backups.view') ?? false
                    ),

                Action::make('download')
                    ->label('تحميل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(
                        fn (Backup $record): bool =>
                            (auth()->user()?->can('backups.download') ?? false)
                            && $record->status === 'completed'
                            && filled($record->file_path)
                            && Storage::disk(
                                config('backup.backup.destination.disks.0', 'local')
                            )->exists($record->file_path)
                    )
                    ->action(function (Backup $record) {
                        abort_unless(
                            auth()->user()?->can('backups.download'),
                            403
                        );

                        abort_unless(
                            $record->status === 'completed'
                            && filled($record->file_path),
                            404
                        );

                        $disk = Storage::disk(
                            config('backup.backup.destination.disks.0', 'local')
                        );

                        abort_unless(
                            $disk->exists($record->file_path),
                            404
                        );

                        return $disk->download(
                            $record->file_path,
                            basename($record->file_path)
                        );
                    }),

                Action::make('restore')
                    ->label('استعادة')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(
                        fn (Backup $record): bool =>
                            (auth()->user()?->can('backups.restore') ?? false)
                            && $record->status === 'completed'
                            && filled($record->file_path)
                            && Storage::disk(
                                config('backup.backup.destination.disks.0', 'local')
                            )->exists($record->file_path)
                    )
                    ->schema(fn (Backup $record): array => [
                        Select::make('scope')
                            ->label('نطاق الاستعادة')
                            ->options(fn (): array => self::getRestoreScopes($record))
                            ->required()
                            ->native(false)
                            ->helperText('يتم تحديد الخيارات وفق محتويات النسخة الاحتياطية.'),
                    ])
                    ->modalHeading('استعادة نسخة احتياطية')
                    ->modalDescription(
                        'سيتم إنشاء نسخة احتياطية وقائية قبل الاستعادة. قد تؤدي الاستعادة إلى استبدال البيانات والملفات الحالية وفق النطاق المحدد.'
                    )
                    ->modalSubmitActionLabel('بدء الاستعادة')
                    ->modalCancelActionLabel('إلغاء')
                    ->action(function (Backup $record, array $data): void {
                        abort_unless(
                            auth()->user()?->can('backups.restore'),
                            403
                        );

                        $disk = Storage::disk(
                            config('backup.backup.destination.disks.0', 'local')
                        );

                        abort_unless(
                            $record->status === 'completed'
                            && filled($record->file_path)
                            && $disk->exists($record->file_path),
                            404
                        );

                        try {
                            $inspection = app(BackupRestoreService::class)
                                ->inspectArchive($disk->path($record->file_path));

                            $availableScopes = array_keys(
                                $inspection['scopes'] ?? []
                            );

                            if (! in_array($data['scope'] ?? null, $availableScopes, true)) {
                                throw new \RuntimeException(
                                    'نطاق الاستعادة المحدد غير متاح لهذه النسخة.'
                                );
                            }

                            app(BackupRestoreService::class)->restore(
                                $record,
                                $data['scope']
                            );

                            Notification::make()
                                ->title('تمت استعادة النسخة الاحتياطية بنجاح')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            report($exception);

                            Notification::make()
                                ->title('تعذرت استعادة النسخة الاحتياطية')
                                ->body($exception->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),

                Action::make('delete')
                    ->label('حذف')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->can('backups.delete') ?? false
                    )
                    ->requiresConfirmation()
                    ->modalHeading('حذف النسخة الاحتياطية')
                    ->modalDescription(
                        'هل أنت متأكد من حذف هذه النسخة الاحتياطية؟ سيتم حذف ملف النسخة وسجلها من النظام.'
                    )
                    ->modalSubmitActionLabel('نعم، حذف')
                    ->modalCancelActionLabel('إلغاء')
                    ->action(function (Backup $record): void {
                        abort_unless(
                            auth()->user()?->can('backups.delete'),
                            403
                        );

                        $disk = Storage::disk(
                            config('backup.backup.destination.disks.0', 'local')
                        );

                        if (
                            filled($record->file_path)
                            && $disk->exists($record->file_path)
                        ) {
                            $disk->delete($record->file_path);
                        }

                        $record->delete();
                    }),
            ])
            ->recordActionsColumnLabel('العمليات')
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('حذف المحدد')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->visible(
                            fn (): bool =>
                                auth()->user()?->can('backups.delete') ?? false
                        )
                        ->requiresConfirmation()
                        ->modalHeading('حذف النسخ الاحتياطية')
                        ->modalDescription(
                            'هل أنت متأكد من حذف النسخ الاحتياطية المحددة؟ سيتم حذف ملفات النسخ وسجلاتها من النظام.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->modalCancelActionLabel('إلغاء')
                        ->action(function ($records): void {
                            abort_unless(
                                auth()->user()?->can('backups.delete'),
                                403
                            );

                            $disk = Storage::disk(
                                config('backup.backup.destination.disks.0', 'local')
                            );

                            foreach ($records as $record) {
                                if (
                                    filled($record->file_path)
                                    && $disk->exists($record->file_path)
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

    private static function getRestoreScopes(Backup $record): array
    {
        try {
            $disk = Storage::disk(
                config('backup.backup.destination.disks.0', 'local')
            );

            if (
                ! filled($record->file_path)
                || ! $disk->exists($record->file_path)
            ) {
                return [];
            }

            $inspection = app(BackupRestoreService::class)
                ->inspectArchive($disk->path($record->file_path));

            return $inspection['scopes'] ?? [];
        } catch (Throwable $exception) {
            report($exception);

            return [];
        }
    }
}
