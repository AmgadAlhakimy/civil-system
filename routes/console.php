<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('backup:automatic', function () {
    $this->call(\App\Console\Commands\AutomaticBackupCommand::class);
})->purpose('تنفيذ النسخ الاحتياطي التلقائي للنظام');

Schedule::command('backup:automatic')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('passports:update-expired')
    ->everyMinute()
    ->withoutOverlapping();
