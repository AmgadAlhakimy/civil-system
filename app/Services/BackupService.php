<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public function create(
        string $type = 'automatic',
        ?string $notes = null,
        ?string $userId = null
    ): Backup {
        $diskName = config(
            'backup.backup.destination.disks.0',
            'local'
        );

        $disk = Storage::disk($diskName);

        $filesBefore = collect($disk->allFiles('backups'))
            ->filter(
                fn (string $file): bool =>
                str_ends_with(strtolower($file), '.zip')
            )
            ->values()
            ->all();

        $path = getenv('PATH') ?: '';

        $systemRoot = getenv('SystemRoot')
            ?: getenv('SYSTEMROOT')
                ?: 'C:\\WINDOWS';

        $windir = getenv('WINDIR')
            ?: getenv('windir')
                ?: 'C:\\WINDOWS';

        putenv('PATH=' . $path);
        putenv('SystemRoot=' . $systemRoot);
        putenv('WINDIR=' . $windir);

        $_ENV['PATH'] = $path;
        $_ENV['SystemRoot'] = $systemRoot;
        $_ENV['WINDIR'] = $windir;

        $_SERVER['PATH'] = $path;
        $_SERVER['SystemRoot'] = $systemRoot;
        $_SERVER['WINDIR'] = $windir;

        $exitCode = Artisan::call('backup:run', [
            '--only-db' => true,
        ]);

        if ($exitCode !== 0) {
            $output = Artisan::output();

            throw new \Exception(
                "فشل إنشاء النسخة الاحتياطية.\n\n" .
                "تفاصيل الأمر:\n" .
                $output
            );
        }

        $filesAfter = collect($disk->allFiles('backups'))
            ->filter(
                fn (string $file): bool =>
                str_ends_with(strtolower($file), '.zip')
            )
            ->values()
            ->all();

        $newFiles = array_values(
            array_diff($filesAfter, $filesBefore)
        );

        if (empty($newFiles)) {
            $newFiles = $filesAfter;
        }

        if (empty($newFiles)) {
            throw new \Exception(
                'تم تنفيذ النسخ الاحتياطي بنجاح، ولكن لم يتم العثور على ملف ZIP.'
            );
        }

        usort(
            $newFiles,
            fn (string $a, string $b): int =>
                $disk->lastModified($b)
                <=> $disk->lastModified($a)
        );

        $filePath = $newFiles[0];

        if (! $disk->exists($filePath)) {
            throw new \Exception(
                'تم إنشاء النسخة الاحتياطية ولكن الملف غير موجود.'
            );
        }

        $fileSize = $disk->size($filePath);

        if ($fileSize <= 0) {
            throw new \Exception(
                'تم إنشاء ملف النسخة الاحتياطية ولكنه فارغ.'
            );
        }

        return Backup::create([
            'name' => basename($filePath),
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'type' => $type,
            'status' => 'completed',
            'notes' => $notes,
            'created_by' => $userId,
            'completed_at' => now(),
        ]);
    }
}
