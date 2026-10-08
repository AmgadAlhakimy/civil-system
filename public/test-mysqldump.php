<?php

$paths = [
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    'C:\\xampp\\mysql\\bin\\mysqldump',
];

echo '<pre>';

foreach ($paths as $path) {
    echo "Path: {$path}\n";
    echo 'Exists: ' . (file_exists($path) ? 'YES' : 'NO') . "\n";
}

echo "\nPATH:\n";
echo getenv('PATH') ?: 'PATH غير متاح';

echo '</pre>';
