<?php

namespace App\Filament\Resources\Backups\Pages;

use App\Filament\Resources\Backups\BackupResource;
use App\Models\Backup;
use App\Services\BackupService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Locked;
use Throwable;

class CreateBackup extends CreateRecord
{
    protected static string $resource = BackupResource::class;

    #[Locked]
    public string $backupScope = 'database';

    public function mount(): void
    {
        $scope = request()->query('scope', 'database');

        abort_unless(
            in_array($scope, ['database', 'full'], true),
            404
        );

        $permission = $scope === 'database'
            ? 'backups.create_database'
            : 'backups.create_full';

        abort_unless(
            auth()->user()?->can($permission),
            403
        );

        parent::mount();

        $this->backupScope = $scope;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('إنشاء نسخة احتياطية')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->requiresConfirmation()
            ->modalHeading('تأكيد إنشاء نسخة احتياطية')
            ->modalDescription(
                $this->backupScope === 'full'
                    ? 'سيتم إنشاء نسخة كاملة تشمل قاعدة البيانات وملفات النظام المهمة. قد تستغرق العملية بعض الوقت.'
                    : 'سيتم إنشاء نسخة من قاعدة البيانات فقط. قد تستغرق العملية بعض الوقت.'
            )
            ->modalSubmitActionLabel('نعم، إنشاء النسخة')
            ->modalCancelActionLabel('إلغاء');
    }

    protected function handleRecordCreation(array $data): Backup
    {
        $permission = $this->backupScope === 'database'
            ? 'backups.create_database'
            : 'backups.create_full';

        abort_unless(
            auth()->user()?->can($permission),
            403
        );

        try {
            return app(BackupService::class)->create(
                type: 'manual',
                notes: $data['notes'] ?? null,
                userId: auth()->id(),
                scope: $this->backupScope
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
