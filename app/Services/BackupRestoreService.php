<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class BackupRestoreService
{
    private array $excludedDirectories = [
        'backups',
        'backup-temp',
        'livewire-tmp',
    ];

    public function __construct(
        private BackupService $backupService
    ) {
    }

    public function inspectArchive(string $archivePath): array
    {
        if (! File::exists($archivePath)) {
            throw new RuntimeException('ملف النسخة الاحتياطية غير موجود.');
        }

        $workingDirectory = storage_path(
            'framework/backup-restore/inspect-' . Str::uuid()
        );

        try {
            File::makeDirectory($workingDirectory, 0755, true);

            $extractedPath = $workingDirectory .
                DIRECTORY_SEPARATOR . 'selected';

            $this->extractArchive($archivePath, $extractedPath);

            $sqlPath = $this->getDatabaseSqlPath($extractedPath);

            $this->validateSqlDump($sqlPath);

            $scopes = [
                'database' => 'قاعدة البيانات فقط',
            ];

            if ($this->supportsFullRestore($extractedPath)) {
                $scopes['full'] = 'النظام كاملًا';
            }

            return [
                'scopes' => $scopes,
                'supports_full' => isset($scopes['full']),
            ];
        } finally {
            File::deleteDirectory($workingDirectory);
        }
    }

    public function restore(
        Backup $backup,
        string $scope = 'full'
    ): void {
        if (! in_array($scope, ['database', 'full'], true)) {
            throw new RuntimeException('نطاق الاستعادة غير صالح.');
        }

        if ($backup->status !== 'completed') {
            throw new RuntimeException('لا يمكن استعادة نسخة لم تكتمل.');
        }

        if (! $backup->file_path) {
            throw new RuntimeException('مسار النسخة الاحتياطية غير موجود.');
        }

        if ($scope === 'full' && $backup->backup_scope !== 'full') {
            throw new RuntimeException(
                'لا يمكن تنفيذ استعادة كاملة من نسخة قاعدة بيانات فقط.'
            );
        }

        $diskName = config(
            'backup.backup.destination.disks.0',
            'local'
        );

        $disk = Storage::disk($diskName);

        if (! $disk->exists($backup->file_path)) {
            throw new RuntimeException(
                'ملف النسخة الاحتياطية غير موجود.'
            );
        }

        $this->restoreArchive(
            $disk->path($backup->file_path),
            $scope,
            $backup->name,
            $backup
        );
    }

    public function restoreExternal(
        string $archivePath,
        string $scope
    ): void {
        if (! in_array($scope, ['database', 'full'], true)) {
            throw new RuntimeException('نطاق الاستعادة غير صالح.');
        }

        if (! File::exists($archivePath)) {
            throw new RuntimeException('ملف النسخة الخارجية غير موجود.');
        }

        $this->restoreArchive(
            $archivePath,
            $scope,
            basename($archivePath)
        );
    }

    private function restoreArchive(
        string $archivePath,
        string $scope,
        string $backupName,
        ?Backup $backup = null
    ): void {
        $lockPath = storage_path('framework/backup-restore.lock');

        File::ensureDirectoryExists(dirname($lockPath));

        $lock = fopen($lockPath, 'c');

        if (! $lock) {
            throw new RuntimeException(
                'تعذر إنشاء ملف قفل عملية الاستعادة.'
            );
        }

        if (! flock($lock, LOCK_EX | LOCK_NB)) {
            fclose($lock);

            throw new RuntimeException(
                'توجد عملية استعادة أخرى قيد التنفيذ.'
            );
        }

        $workingDirectory = storage_path(
            'framework/backup-restore/' . Str::uuid()
        );

        $filesChanged = false;
        $preserveWorkingDirectory = false;

        try {
            if (! File::exists($archivePath)) {
                throw new RuntimeException(
                    'ملف النسخة الاحتياطية غير موجود.'
                );
            }

            File::makeDirectory($workingDirectory, 0755, true);

            $extractedPath = $workingDirectory .
                DIRECTORY_SEPARATOR . 'selected';

            $this->extractArchive($archivePath, $extractedPath);

            $sqlPath = $this->getDatabaseSqlPath($extractedPath);

            $this->validateSqlDump($sqlPath);

            $availableScopes = $this->getAvailableScopes($extractedPath);

            if (! in_array($scope, $availableScopes, true)) {
                throw new RuntimeException(
                    $scope === 'full'
                        ? 'لا يمكن استعادة النظام كاملًا؛ النسخة لا تحتوي على ملفات النظام المطلوبة.'
                        : 'نطاق الاستعادة غير متاح في هذه النسخة.'
                );
            }

            $this->validateDatabaseConnection();

            $diskName = config(
                'backup.backup.destination.disks.0',
                'local'
            );

            $disk = Storage::disk($diskName);

            $safetyBackup = $this->backupService->create(
                'automatic',
                'نسخة وقائية قبل استعادة النسخة: ' . $backupName,
                auth()->id(),
                'full'
            );

            if (
                $safetyBackup->status !== 'completed' ||
                ! $safetyBackup->file_path ||
                ! $disk->exists($safetyBackup->file_path)
            ) {
                throw new RuntimeException(
                    'تعذر إنشاء النسخة الوقائية. تم إلغاء الاستعادة.'
                );
            }

            $safetyPath = $disk->path($safetyBackup->file_path);

            $rollbackExtractedPath = $workingDirectory .
                DIRECTORY_SEPARATOR . 'rollback';

            $this->extractArchive(
                $safetyPath,
                $rollbackExtractedPath
            );

            $rollbackSqlPath = $this->getDatabaseSqlPath(
                $rollbackExtractedPath
            );

            $this->validateSqlDump($rollbackSqlPath);

            if (
                $scope === 'full' &&
                ! $this->supportsFullRestore($rollbackExtractedPath)
            ) {
                throw new RuntimeException(
                    'النسخة الوقائية لا تحتوي على ملفات النظام المطلوبة للتراجع.'
                );
            }

            try {
                if ($scope === 'full') {
                    $filesChanged = true;

                    $this->restoreFiles($extractedPath);
                }

                $this->restoreDatabase($sqlPath);

            } catch (Throwable $exception) {
                try {
                    if ($filesChanged) {
                        $this->restoreFiles($rollbackExtractedPath);
                    }

                    $this->restoreDatabase($rollbackSqlPath);

                } catch (Throwable $rollbackException) {
                    $preserveWorkingDirectory = true;

                    throw new RuntimeException(
                        "فشلت الاستعادة وفشلت محاولة التراجع أيضًا.\n" .
                        "خطأ الاستعادة: {$exception->getMessage()}\n" .
                        "خطأ التراجع: {$rollbackException->getMessage()}\n" .
                        "ملفات الاستعادة محفوظة مؤقتًا في: {$workingDirectory}",
                        0,
                        $exception
                    );
                }

                throw new RuntimeException(
                    'فشلت الاستعادة وتمت إعادة الحالة السابقة. ' .
                    $exception->getMessage(),
                    0,
                    $exception
                );
            }

            activity('backups')
                ->causedBy(auth()->user())
                ->withProperties([
                    'backup_id' => $backup?->id,
                    'backup_name' => $backupName,
                    'restore_source' => $backup ? 'registered' : 'external',
                    'restore_scope' => $scope,
                    'safety_backup_id' => $safetyBackup->id,
                ])
                ->log('تم استعادة نسخة احتياطية بنجاح.');

        } finally {
            if (! $preserveWorkingDirectory) {
                File::deleteDirectory($workingDirectory);
            }

            flock($lock, LOCK_UN);

            fclose($lock);
        }
    }

    private function getAvailableScopes(string $extractedPath): array
    {
        $scopes = ['database'];

        if ($this->supportsFullRestore($extractedPath)) {
            $scopes[] = 'full';
        }

        return $scopes;
    }

    private function supportsFullRestore(string $extractedPath): bool
    {
        return
            File::isDirectory(
                $extractedPath . DIRECTORY_SEPARATOR . 'private'
            ) &&
            File::isDirectory(
                $extractedPath . DIRECTORY_SEPARATOR . 'public'
            );
    }

    private function getDatabaseSqlPath(string $extractedPath): string
    {
        $dumpDirectory = $extractedPath .
            DIRECTORY_SEPARATOR . 'db-dumps';

        if (! File::isDirectory($dumpDirectory)) {
            throw new RuntimeException(
                'مجلد قاعدة البيانات db-dumps غير موجود داخل النسخة.'
            );
        }

        $sqlFiles = array_values(
            array_filter(
                File::files($dumpDirectory),
                fn ($file): bool => strtolower($file->getExtension()) === 'sql'
            )
        );

        if (count($sqlFiles) === 0) {
            throw new RuntimeException(
                'لم يتم العثور على ملف SQL داخل النسخة الاحتياطية.'
            );
        }

        if (count($sqlFiles) > 1) {
            throw new RuntimeException(
                'تحتوي النسخة على أكثر من ملف SQL، ولا يمكن تحديد قاعدة البيانات المطلوبة تلقائيًا.'
            );
        }

        return $sqlFiles[0]->getPathname();
    }

    private function extractArchive(
        string $archivePath,
        string $destination
    ): void {
        $zip = new ZipArchive();

        $result = $zip->open($archivePath, ZipArchive::CHECKCONS);

        if ($result !== true) {
            throw new RuntimeException(
                'تعذر فتح النسخة الاحتياطية أو أن الأرشيف غير سليم.'
            );
        }

        try {
            $password = config('backup.backup.password');

            if (! is_string($password) || $password === '') {
                throw new RuntimeException(
                    'كلمة مرور النسخ الاحتياطي غير مضبوطة.'
                );
            }

            if (! $zip->setPassword($password)) {
                throw new RuntimeException(
                    'تعذر ضبط كلمة مرور النسخة الاحتياطية.'
                );
            }

            File::makeDirectory($destination, 0755, true);

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);

                if ($entry === false) {
                    throw new RuntimeException(
                        'تعذر قراءة أحد عناصر النسخة الاحتياطية.'
                    );
                }

                $relativePath = str_replace('\\', '/', $entry);

                if (
                    $relativePath === '' ||
                    str_starts_with($relativePath, '/') ||
                    preg_match('/^[A-Za-z]:/', $relativePath) ||
                    in_array('..', explode('/', $relativePath), true) ||
                    str_contains($relativePath, "\0")
                ) {
                    throw new RuntimeException(
                        'تم العثور على مسار غير آمن داخل النسخة الاحتياطية.'
                    );
                }

                if (
                    $relativePath !== '.gitignore' &&
                    ! preg_match(
                        '#^(db-dumps|private|public|reports)/#',
                        $relativePath
                    )
                ) {
                    throw new RuntimeException(
                        'مسار غير متوقع داخل النسخة الاحتياطية: ' .
                        $relativePath
                    );
                }

                if (
                    preg_match(
                        '#^private/(backups|backup-temp|livewire-tmp)(/|$)#',
                        $relativePath
                    )
                ) {
                    continue;
                }

                $targetPath = $destination . DIRECTORY_SEPARATOR .
                    str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

                if (str_ends_with($relativePath, '/')) {
                    File::makeDirectory($targetPath, 0755, true);
                    continue;
                }

                File::makeDirectory(dirname($targetPath), 0755, true);

                $input = $zip->getStream($entry);

                if ($input === false) {
                    throw new RuntimeException(
                        'تعذر فك تشفير الملف: ' . $relativePath
                    );
                }

                $output = fopen($targetPath, 'wb');

                if ($output === false) {
                    fclose($input);

                    throw new RuntimeException(
                        'تعذر إنشاء الملف: ' . $relativePath
                    );
                }

                try {
                    if (stream_copy_to_stream($input, $output) === false) {
                        throw new RuntimeException(
                            'حدث خطأ أثناء استخراج الملف: ' . $relativePath
                        );
                    }
                } finally {
                    fclose($input);
                    fclose($output);
                }
            }
        } finally {
            $zip->close();
        }
    }

    private function validateSqlDump(string $sqlPath): void
    {
        if (! File::exists($sqlPath) || File::size($sqlPath) <= 0) {
            throw new RuntimeException(
                'ملف قاعدة البيانات غير موجود أو فارغ.'
            );
        }

        $file = fopen($sqlPath, 'rb');

        if ($file === false) {
            throw new RuntimeException(
                'تعذر فتح ملف SQL للتحقق منه.'
            );
        }

        $hasCreateTable = false;

        try {
            while (($line = fgets($file)) !== false) {
                if (
                    preg_match(
                        '/^\s*(?:\/\*!\s*\d+\s*)?CREATE\s+TABLE\b/i',
                        $line
                    )
                ) {
                    $hasCreateTable = true;
                }

                if (
                    preg_match(
                        '/^\s*(?:\/\*!\s*\d+\s*)?(?:CREATE\s+(?:DATABASE|SCHEMA)\b|USE\s+[`"\']?[a-zA-Z0-9_$-]+[`"\']?\s*;)/i',
                        $line
                    )
                ) {
                    throw new RuntimeException(
                        'ملف SQL يحتوي على أمر لتغيير قاعدة البيانات المستهدفة: ' .
                        trim($line)
                    );
                }
            }
        } finally {
            fclose($file);
        }

        if (! $hasCreateTable) {
            throw new RuntimeException(
                'ملف SQL لا يحتوي على أوامر إنشاء الجداول المتوقعة.'
            );
        }
    }

    private function validateDatabaseConnection(): void
    {
        $connectionName = config('database.default');

        $connection = config(
            "database.connections.{$connectionName}"
        );

        if (
            ! is_array($connection) ||
            ($connection['driver'] ?? null) !== 'mysql'
        ) {
            throw new RuntimeException(
                'الاستعادة مدعومة حاليًا لاتصالات MySQL فقط.'
            );
        }

        if (
            empty($connection['database']) ||
            empty($connection['username'])
        ) {
            throw new RuntimeException(
                'إعدادات اتصال قاعدة البيانات غير مكتملة.'
            );
        }

        try {
            DB::connection($connectionName)->getPdo();
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'تعذر الاتصال بقاعدة البيانات الحالية: ' .
                $exception->getMessage(),
                0,
                $exception
            );
        }

        $binary = $this->getMysqlBinary();

        $process = new Process([$binary, '--version']);

        $process->setTimeout(10);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(
                'تعذر تشغيل برنامج MySQL: ' .
                trim($process->getErrorOutput())
            );
        }
    }

    private function restoreDatabase(string $sqlPath): void
    {
        $connectionName = config('database.default');

        $connection = config(
            "database.connections.{$connectionName}"
        );

        if (
            ! is_array($connection) ||
            ($connection['driver'] ?? null) !== 'mysql'
        ) {
            throw new RuntimeException(
                'الاستعادة مدعومة حاليًا لاتصالات MySQL فقط.'
            );
        }

        $database = $connection['database'] ?? null;
        $username = $connection['username'] ?? null;
        $password = $connection['password'] ?? '';
        $host = $connection['host'] ?? '127.0.0.1';
        $port = $connection['port'] ?? 3306;

        if (! is_string($database) || $database === '') {
            throw new RuntimeException(
                'اسم قاعدة البيانات غير مضبوط.'
            );
        }

        if (! is_string($username) || $username === '') {
            throw new RuntimeException(
                'اسم مستخدم قاعدة البيانات غير مضبوط.'
            );
        }

        $binary = $this->getMysqlBinary();

        $input = fopen($sqlPath, 'rb');

        if ($input === false) {
            throw new RuntimeException(
                'تعذر فتح ملف SQL للاستيراد.'
            );
        }

        try {
            $process = new Process(
                [
                    $binary,
                    '--default-character-set=utf8mb4',
                    '--host=' . $host,
                    '--port=' . $port,
                    '--user=' . $username,
                    '--database=' . $database,
                ],
                base_path(),
                [
                    'MYSQL_PWD' => (string) $password,
                ],
                $input
            );

            $process->setTimeout(null);

            $process->run();

            if (! $process->isSuccessful()) {
                $error = trim($process->getErrorOutput());

                throw new RuntimeException(
                    'فشل استيراد قاعدة البيانات باستخدام MySQL. ' .
                    ($error !== '' ? $error : $process->getOutput())
                );
            }
        } finally {
            fclose($input);
        }
    }

    private function getMysqlBinary(): string
    {
        $configuredBinary = getenv('MYSQL_BINARY');

        if (
            is_string($configuredBinary) &&
            $configuredBinary !== ''
        ) {
            if (! File::exists($configuredBinary)) {
                throw new RuntimeException(
                    'مسار برنامج MySQL المحدد في MYSQL_BINARY غير صحيح.'
                );
            }

            return $configuredBinary;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $xamppRoot = dirname(dirname(PHP_BINARY));

            $xamppMysql = $xamppRoot .
                DIRECTORY_SEPARATOR . 'mysql' .
                DIRECTORY_SEPARATOR . 'bin' .
                DIRECTORY_SEPARATOR . 'mysql.exe';

            if (File::exists($xamppMysql)) {
                return $xamppMysql;
            }
        }

        return 'mysql';
    }

    private function restoreFiles(string $extractedPath): void
    {
        $this->syncDirectory(
            $extractedPath . DIRECTORY_SEPARATOR . 'private',
            storage_path('app/private'),
            $this->excludedDirectories
        );

        $this->syncDirectory(
            $extractedPath . DIRECTORY_SEPARATOR . 'public',
            storage_path('app/public')
        );

        $sourceReports = $extractedPath .
            DIRECTORY_SEPARATOR . 'reports';

        if (File::isDirectory($sourceReports)) {
            $this->syncDirectory(
                $sourceReports,
                storage_path('app/reports')
            );
        }

        $sourceGitignore = $extractedPath .
            DIRECTORY_SEPARATOR . '.gitignore';

        if (File::exists($sourceGitignore)) {
            File::copy(
                $sourceGitignore,
                storage_path('app/.gitignore')
            );
        }
    }

    private function syncDirectory(
        string $source,
        string $destination,
        array $excluded = []
    ): void {
        if (! File::isDirectory($source)) {
            return;
        }

        File::ensureDirectoryExists($destination);

        foreach (File::directories($destination) as $directory) {
            if (in_array(basename($directory), $excluded, true)) {
                continue;
            }

            File::deleteDirectory($directory);
        }

        foreach (File::files($destination) as $file) {
            File::delete($file->getPathname());
        }

        foreach (File::directories($source) as $directory) {
            $name = basename($directory);

            if (in_array($name, $excluded, true)) {
                continue;
            }

            File::copyDirectory(
                $directory,
                $destination . DIRECTORY_SEPARATOR . $name
            );
        }

        foreach (File::files($source) as $file) {
            File::copy(
                $file->getPathname(),
                $destination . DIRECTORY_SEPARATOR . $file->getFilename()
            );
        }
    }
}
