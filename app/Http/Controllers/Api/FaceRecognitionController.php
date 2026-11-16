<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use App\Models\FaceScanLog;
use App\Services\FaceEmbeddingService;
use App\Services\FaceMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceRecognitionController extends Controller
{
    public function __construct(
        private readonly FaceEmbeddingService $embeddingService,
        private readonly FaceMatchService $matchService,
    ) {}

    public function identify(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $user = $request->user();
        $tempPath = $request->file('image')->store('temp/face_scans', 'local');
        $content  = Storage::disk('local')->get($tempPath);

        $extraction = $this->embeddingService->extract($content);
        Storage::disk('local')->delete($tempPath);

        if (! $extraction['success']) {
            FaceScanLog::create([
                'user_id'    => $user->id,
                'status'     => 'no_face',
                'matched'    => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => match ($extraction['error']) {
                    'no_face_detected'      => 'لم يتم اكتشاف وجه واضح في الصورة.',
                    'ai_service_unavailable' => 'خدمة التعرف غير متاحة حالياً.',
                    default                 => 'فشل تحليل الصورة.',
                },
            ], 422);
        }

        $result = $this->matchService->findBestMatch($extraction['embedding']);

        FaceScanLog::create([
            'user_id'            => $user->id,
            'matched_citizen_id' => $result['citizen']?->id,
            'confidence'         => $result['confidence'],
            'matched'            => $result['citizen'] !== null,
            'status'             => $result['citizen'] ? 'success' : 'no_match',
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
        ]);

        if ($result['citizen'] === null) {
            return response()->json([
                'status'     => 'error',
                'message'    => 'لم يتم التعرف على الشخص. تأكد من وضوح الصورة أو أن المواطن مسجل في النظام.',
                'confidence' => $result['confidence'],
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'تم التعرف على المواطن.',
            'data'    => $this->formatCitizen($result['citizen'], $result['confidence']),
        ]);
    }




    
    private function formatCitizen(Citizen $citizen, float $confidence): array
    {
        $nationalId = \DB::table('identity_cards')
            ->where('citizen_id', $citizen->id)
            ->where('status', 'active')
            ->value('national_id');

        return [
            'id'          => $citizen->id,
            'full_name'   => trim(sprintf(
                '%s %s %s %s',
                $citizen->first_name,
                $citizen->father_name,
                $citizen->middle_name,
                $citizen->last_name,
            )),
            'national_id' => $nationalId,
            'birth_date'  => $citizen->birth_date?->toDateString(),
            'gender'      => $citizen->gender,
            'address'     => $citizen->address,
            'phone'       => $citizen->phone,


            'photo_url' => $citizen->photo
    ? url("/citizens/photo/" . basename($citizen->photo))
    : null,


            'verification_status' => $citizen->verification_status,
            'confidence'  => round($confidence, 4),
        ];
    }
}