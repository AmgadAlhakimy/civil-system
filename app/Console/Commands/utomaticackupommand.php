<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use App\Services\SettingService;
use Illuminate\Console\Command;
use Throwable;

class AutomaticBackupCommand extends Command
{
    protected $signature = 'backup:automatic';

    protected $description = 'تنفيذ النسخ الاحتياطي التلقائي للنظام';

    public function handle(
        SettingService $settings,
        BackupService $backupService
    ): int {
        $enabled = filter_var(
            $settings->get(
                'automatic_backup_enabled',
                true
            ),
            FILTER_VALIDATE_BOOLEAN
        );

        if (! $enabled) {
            $this->info(
                'النسخ الاحتياطي التلقائي غير مفعل.'
            );

            return self::SUCCESS;
        }

        $frequency = $settings->get(
            'automatic_backup_frequency',
            'daily'
        );

        $time = $settings->get(
            'automatic_backup_time',
            '03:00'
        );

        $timezone = $settings->get(
            'timezone',
            'Asia/Aden'
        );

        $now = now($timezone);

        $configuredTime = substr(
            (string) $time,
            0,
            5
        );

        $currentTime = $now->format('H:i');

        if ($currentTime !== $configuredTime) {
            $this->info(
                "ليس وقت النسخ الاحتياطي. الوقت الحالي: {$currentTime}، الوقت المحدد: {$configuredTime}"
            );

            return self::SUCCESS;
        }

        if (
            $frequency === 'weekly' &&
            ! $now->isMonday()
        ) {
            $this->info(
                'النسخ الاحتياطي الأسبوعي مقرر ليوم الاثنين.'
            );

            return self::SUCCESS;
        }

        if (
            $frequency === 'monthly' &&
            $now->day !== 1
        ) {
            $this->info(
                'النسخ الاحتياطي الشهري مقرر لليوم الأول من الشهر.'
            );

            return self::SUCCESS;
        }

        try {
            $backup = $backupService->create(
                type: 'automatic',
                notes: 'نسخة احتياطية تلقائية تم إنشاؤها بواسطة النظام.',
                userId: null
            );

            $this->info(
                'تم إنشاء النسخة الاحتياطية التلقائية بنجاح.'
            );

            $this->line(
                'الملف: ' . $backup->file_path
            );

            $this->line(
                'الحجم: ' . $backup->file_size . ' بايت'
            );

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error(
                'فشل إنشاء النسخة الاحتياطية التلقائية.'
            );

            $this->error(
                $e->getMessage()
            );

            return self::FAILURE;
        }
    }
}
