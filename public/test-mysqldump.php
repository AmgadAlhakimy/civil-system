<?php

$output = [];
$code = 0;

$backupPath = 'C:\\xampp\\htdocs\\CivilSystem\\storage\\app\\private\\test_backup.sql';

$command = 'mysqldump -h 127.0.0.1 -P 3306 -u root civil_registry > "' . $backupPath . '" 2>&1';

exec($command, $output, $code);

echo '<pre>';
echo 'Exit code: ' . $code . PHP_EOL;
echo 'Output:' . PHP_EOL;
echo implode(PHP_EOL, $output);
echo '</pre>';
