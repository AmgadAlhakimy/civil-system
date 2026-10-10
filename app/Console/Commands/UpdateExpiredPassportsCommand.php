<?php

namespace App\Console\Commands;

use App\Models\Passport;
use Illuminate\Console\Command;

class UpdateExpiredPassportsCommand extends Command
{
    protected $signature = 'passports:update-expired';

    protected $description = 'تحديث حالة الجوازات التي انتهت صلاحيتها';

    public function handle(): int
    {
        $updated = Passport::query()
            ->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today())
            ->update([
                'status' => 'expired',
            ]);

        $this->info("تم تحديث حالة {$updated} جواز منتهٍ.");

        return self::SUCCESS;
    }
}
