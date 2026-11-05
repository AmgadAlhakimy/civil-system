<?php

namespace App\Console\Commands;

use App\Models\Citizen;
use App\Services\FaceEmbeddingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class IndexCitizensFaces extends Command
{
    protected $signature = 'face:index-citizens
                            {--force : إعادة توليد حتى لو موجودة}
                            {--limit=0 : حد أقصى (0 = الكل)}
                            {--chunk=50 : حجم الدفعة}';

    protected $description = 'توليد face embeddings لكل المواطنين الذين لديهم صور';



public function handle(FaceEmbeddingService $service): int
{
    $query = Citizen::query()
        ->whereNotNull('photo')
        ->where('photo', '!=', '');

    if (! $this->option('force')) {
        $query->where(function ($q) {
            $q->whereNull('face_data')->orWhere('face_data', '');
        });
    }

    // ✅ إصلاح limit: استخدم get() بدلاً من chunkById
    $limit = (int) $this->option('limit');

    if ($limit > 0) {
        $citizens = $query->limit($limit)->get();
    } else {
        $citizens = $query->get();
    }

    $total = $citizens->count();
    $this->info("📊 إجمالي المواطنين للمعالجة: {$total}");

    if ($total === 0) {
        $this->warn('لا يوجد مواطنون للمعالجة.');
        return self::SUCCESS;
    }

    $bar = $this->output->createProgressBar($total);
    $bar->start();

    $success = 0;
    $noFace = 0;
    $missingPhoto = 0;
    $aiError = 0;

    foreach ($citizens as $citizen) {
        $path = $citizen->photo;
        $content = null;

        if (Storage::disk('public')->exists($path)) {
            $content = Storage::disk('public')->get($path);
        } elseif (Storage::disk('local')->exists($path)) {
            $content = Storage::disk('local')->get($path);
        } elseif (Storage::disk('local')->exists("public/{$path}")) {
            $content = Storage::disk('local')->get("public/{$path}");
        }

        if ($content === null) {
            $missingPhoto++;
            $bar->advance();
            continue;
        }

        $result = $service->extract($content, basename($citizen->photo));

        if (! $result['success']) {
            if ($result['error'] === 'no_face_detected') {
                $noFace++;
            } else {
                $aiError++;
            }
            $bar->advance();
            continue;
        }

        $citizen->update([
            'face_data' => json_encode($result['embedding']),
        ]);
        $success++;

        usleep(50_000);
        $bar->advance();
    }

    $bar->finish();
    $this->newLine(2);

    $this->table(
        ['الحالة', 'العدد'],
        [
            ['✅ نجح', $success],
            ['⚠️ لا يوجد وجه', $noFace],
            ['❌ صورة مفقودة', $missingPhoto],
            ['🔌 خطأ في الخدمة', $aiError],
        ],
    );

    return self::SUCCESS;
}




}