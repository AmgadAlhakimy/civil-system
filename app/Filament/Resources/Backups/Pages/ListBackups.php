<?php

namespace App\Filament\Resources\Backups\Pages;

use App\Filament\Resources\Backups\BackupResource;
use App\Services\BackupRestoreService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ListBackups extends ListRecords
{
    protected static string $resource = BackupResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('backups.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createDatabaseBackup')
                ->label('إنشاء نسخة لقاعدة البيانات')
                ->icon('heroicon-o-circle-stack')
                ->url(
                    fn (): string => BackupResource::getUrl('create', [
                        'scope' => 'database',
                    ])
                )
                ->visible(
                    fn (): bool => auth()->user()?->can('backups.create_database') ?? false
                ),

            Action::make('createFullBackup')
                ->label('إنشاء نسخة كاملة للنظام')
                ->icon('heroicon-o-server-stack')
                ->color('success')
                ->url(
                    fn (): string => BackupResource::getUrl('create', [
                        'scope' => 'full',
                    ])
                )
                ->visible(
                    fn (): bool => auth()->user()?->can('backups.create_full') ?? false
                ),

            Action::make('restoreExternal')
                ->label('استعادة نسخة خارجية')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->visible(
                    fn (): bool => auth()->user()?->can('backups.restore') ?? false
                )
                ->schema([
                    FileUpload::make('archive')
                        ->label('ملف النسخة الاحتياطية')
                        ->disk('local')
                        ->directory('backup-temp/external-restore')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/zip',
                            'application/x-zip-compressed',
                        ])
                        ->maxSize(512000)
                        ->required()
                        ->live(),

                    Select::make('scope')
                        ->label('نطاق الاستعادة')
                        ->options(function (Get $get): array {
                            $archivePath = $get('archive');

                            if (! is_string($archivePath) || $archivePath === '') {
                                return [];
                            }

                            $disk = Storage::disk('local');

                            if (! $disk->exists($archivePath)) {
                                return [];
                            }

                            try {
                                $inspection = app(BackupRestoreService::class)
                                    ->inspectArchive($disk->path($archivePath));

                                return $inspection['scopes'] ?? [];
                            } catch (Throwable) {
                                return [];
                            }
                        })
                        ->required()
                        ->disabled(
                            fn (Get $get): bool => blank($get('archive'))
                        )
                        ->helperText(
                            'يحدد النظام خيارات الاستعادة تلقائيًا حسب محتويات النسخة الاحتياطية.'
                        ),
                ])
                ->modalHeading('استعادة نسخة احتياطية خارجية')
                ->modalDescription(
                    'ارفع ملف النسخة الاحتياطية. سيفحص النظام محتوياته ويعرض نطاقات الاستعادة المتاحة. ستُنشأ نسخة وقائية قبل تنفيذ الاستعادة.'
                )
                ->modalSubmitActionLabel('بدء الاستعادة')
                ->modalCancelActionLabel('إلغاء')
                ->action(function (array $data): void {
                    abort_unless(
                        auth()->user()?->can('backups.restore'),
                        403
                    );

                    $disk = Storage::disk('local');
                    $archivePath = $data['archive'] ?? null;

                    if (! is_string($archivePath) || $archivePath === '') {
                        Notification::make()
                            ->title('لم يتم تحديد ملف النسخة الاحتياطية')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        if (! $disk->exists($archivePath)) {
                            throw new \RuntimeException(
                                'ملف النسخة الاحتياطية غير موجود.'
                            );
                        }

                        $service = app(BackupRestoreService::class);

                        $absolutePath = $disk->path($archivePath);

                        $inspection = $service->inspectArchive($absolutePath);

                        $availableScopes = array_keys(
                            $inspection['scopes'] ?? []
                        );

                        if (! in_array($data['scope'] ?? null, $availableScopes, true)) {
                            throw new \RuntimeException(
                                'نطاق الاستعادة المختار غير متاح في محتويات النسخة الاحتياطية.'
                            );
                        }

                        $service->restoreExternal(
                            $absolutePath,
                            $data['scope']
                        );

                        Notification::make()
                            ->title('تمت الاستعادة بنجاح')
                            ->success()
                            ->send();

                    } catch (Throwable $exception) {
                        Notification::make()
                            ->title('تعذرت استعادة النسخة الاحتياطية')
                            ->body($exception->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();

                    } finally {
                        if ($disk->exists($archivePath)) {
                            $disk->delete($archivePath);
                        }
                    }
                }),
        ];
    }
}
