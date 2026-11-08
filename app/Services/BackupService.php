<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BackupService
{
    public function create(
        string $type = 'automatic',
        ?string $notes = null,
        ?string $userId = null,
        string $scope = 'database'
    ): Backup {
        if (! in_array($scope, ['database', 'full'], true)) {
            throw new RuntimeException('نطاق النسخة الاحتياطية غير صالح.');
        }

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
            ->mapWithKeys(
                fn (string $file): array => [
                    $file => [
                        'last_modified' => $disk->lastModified($file),
                        'size' => $disk->size($file),
                    ],
                ]
            )
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

        $commandOptions = $scope === 'database'
            ? ['--only-db' => true]
            : [];

        $exitCode = Artisan::call('backup:run', $commandOptions);

        if ($exitCode !== 0) {
            $output = Artisan::output();

            throw new RuntimeException(
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

        $newFiles = collect($filesAfter)
            ->filter(function (string $file) use ($disk, $filesBefore): bool {
                if (! array_key_exists($file, $filesBefore)) {
                    return true;
                }

                return $disk->lastModified($file) !== $filesBefore[$file]['last_modified']
                    || $disk->size($file) !== $filesBefore[$file]['size'];
            })
            ->values()
            ->all();

        if (empty($newFiles)) {
            throw new RuntimeException(
                'تم تنفيذ أمر النسخ الاحتياطي، ولكن لم يتم العثور على ملف ZIP جديد.'
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
            throw new RuntimeException(
                'تم إنشاء النسخة الاحتياطية ولكن الملف غير موجود.'
            );
        }

        $fileSize = $disk->size($filePath);

        if ($fileSize <= 0) {
            throw new RuntimeException(
                'تم إنشاء ملف النسخة الاحتياطية ولكنه فارغ.'
            );
        }

        return Backup::create([
            'name' => basename($filePath),
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'type' => $type,
            'backup_scope' => $scope,
            'status' => 'completed',
            'notes' => $notes,
            'created_by' => $userId,
            'completed_at' => now(),
        ]);
    }
}
