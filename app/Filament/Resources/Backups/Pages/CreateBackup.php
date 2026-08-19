<?php

namespace App\Filament\Resources\Backups\Pages;

use App\Filament\Resources\Backups\BackupResource;
use App\Models\Backup;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
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
        $userId = auth()->id();

        try {
            $diskName = config(
                'backup.backup.destination.disks.0',
                'local'
            );

            $disk = Storage::disk($diskName);

            /*
             * الملفات الموجودة قبل إنشاء النسخة
             */
            $filesBefore = $disk->allFiles('backups');

            /*
             * إنشاء النسخة فعليًا بواسطة Spatie
             */
            $exitCode = Artisan::call('backup:run');

            /*
             * التأكد من نجاح الأمر
             */
            if ($exitCode !== 0) {
                $output = Artisan::output();

                throw new \Exception(
                    "فشل إنشاء النسخة الاحتياطية.\n\n" .
                    "تفاصيل الأمر:\n" .
                    $output
                );
            }

            /*
             * الملفات الموجودة بعد إنشاء النسخة
             */
            $filesAfter = $disk->allFiles('backups');

            /*
             * البحث عن ملفات ZIP الجديدة فقط
             */
            $newFiles = array_values(
                array_diff(
                    $filesAfter,
                    $filesBefore
                )
            );

            $newFiles = array_values(
                array_filter(
                    $newFiles,
                    fn ($file) =>
                    str_ends_with(
                        strtolower($file),
                        '.zip'
                    )
                )
            );

            /*
             * يجب أن يكون هناك ملف ZIP جديد
             */
            if (empty($newFiles)) {
                throw new \Exception(
                    'تم إنشاء النسخة الاحتياطية بنجاح، ولكن لم يتم العثور على ملف ZIP جديد.'
                );
            }

            /*
             * ترتيب الملفات الجديدة حسب آخر تعديل
             */
            usort(
                $newFiles,
                fn ($a, $b) =>
                    $disk->lastModified($b)
                    <=> $disk->lastModified($a)
            );

            $filePath = $newFiles[0];

            /*
             * التأكد من وجود الملف
             */
            if (!$disk->exists($filePath)) {
                throw new \Exception(
                    'تم إنشاء النسخة الاحتياطية ولكن الملف غير موجود.'
                );
            }

            /*
             * حجم الملف
             */
            $fileSize = $disk->size($filePath);

            if ($fileSize <= 0) {
                throw new \Exception(
                    'تم إنشاء ملف النسخة الاحتياطية ولكنه فارغ.'
                );
            }

            /*
             * تسجيل البيانات في جدول backups
             * بعد نجاح إنشاء الملف فقط
             */
            return Backup::create([
                'name' => basename($filePath),
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'type' => $data['type'] ?? 'manual',
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
                'completed_at' => now(),
            ]);

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
