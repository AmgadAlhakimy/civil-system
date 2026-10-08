<?php

namespace App\Filament\Resources\Backups\Pages;

use App\Filament\Resources\Backups\BackupResource;
use App\Models\Backup;
use App\Services\BackupService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Throwable;

class CreateBackup extends CreateRecord
{
    protected static string $resource = BackupResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('إنشاء نسخة احتياطية')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->requiresConfirmation()
            ->modalHeading('تأكيد إنشاء نسخة احتياطية')
            ->modalDescription(
                'هل أنت متأكد من إنشاء نسخة احتياطية؟ سيتم نسخ قاعدة البيانات وملفات النظام وقد تستغرق العملية بعض الوقت.'
            )
            ->modalSubmitActionLabel('نعم، إنشاء النسخة')
            ->modalCancelActionLabel('إلغاء');
    }

    protected function handleRecordCreation(array $data): Backup
    {
        try {
            return app(BackupService::class)->create(
                type: 'manual',
                notes: $data['notes'] ?? null,
                userId: auth()->id()
            );
        } catch (Throwable $e) {
            Notification::make()
                ->title('فشل إنشاء النسخة الاحتياطية')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();

            throw $e;
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء النسخة الاحتياطية بنجاح';
    }
}
