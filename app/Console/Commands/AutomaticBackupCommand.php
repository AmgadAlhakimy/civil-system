<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Services\BackupService;
use App\Services\SettingService;
use Carbon\Carbon;
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
            $settings->get('automatic_backup_enabled', true),
            FILTER_VALIDATE_BOOLEAN
        );

        if (! $enabled) {
            $this->info('النسخ الاحتياطي التلقائي غير مفعل.');

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

        $configuredTime = substr((string) $time, 0, 5);

        if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $configuredTime)) {
            $this->error('وقت النسخ الاحتياطي المحدد غير صالح.');

            return self::FAILURE;
        }

        $currentTime = $now->format('H:i');

        if ($currentTime < $configuredTime) {
            $this->info('لم يحِن وقت النسخ الاحتياطي بعد.');

            return self::SUCCESS;
        }

        if ($frequency === 'weekly' && ! $now->isMonday()) {
            $this->info('النسخ الاحتياطي الأسبوعي مقرر ليوم الاثنين.');

            return self::SUCCESS;
        }

        if ($frequency === 'monthly' && $now->day !== 1) {
            $this->info('النسخ الاحتياطي الشهري مقرر لليوم الأول من الشهر.');

            return self::SUCCESS;
        }

        if (! in_array($frequency, ['daily', 'weekly', 'monthly'], true)) {
            $this->error('تكرار النسخ الاحتياطي غير صالح.');

            return self::FAILURE;
        }

        $periodStart = match ($frequency) {
            'daily' => $now->copy()->startOfDay(),
            'weekly' => $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
            'monthly' => $now->copy()->startOfMonth(),
        };

        $periodEnd = match ($frequency) {
            'daily' => $now->copy()->endOfDay(),
            'weekly' => $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
            'monthly' => $now->copy()->endOfMonth(),
        };

        $alreadyExists = Backup::query()
            ->where('type', 'automatic')
            ->whereBetween('created_at', [
                $periodStart,
                $periodEnd,
            ])
            ->exists();

        if ($alreadyExists) {
            $this->info('تم إنشاء نسخة احتياطية تلقائية لهذه الفترة بالفعل.');

            return self::SUCCESS;
        }

        try {
            $backup = $backupService->create(
                type: 'automatic',
                notes: 'نسخة احتياطية تلقائية تم إنشاؤها بواسطة النظام.',
                userId: null
            );

            $this->info('تم إنشاء النسخة الاحتياطية التلقائية بنجاح.');

            $this->line('الملف: ' . $backup->file_path);

            $this->line('الحجم: ' . $backup->file_size . ' بايت');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('فشل إنشاء النسخة الاحتياطية التلقائية.');

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
