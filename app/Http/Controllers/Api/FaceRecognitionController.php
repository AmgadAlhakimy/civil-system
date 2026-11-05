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
    // ═══════════════════════════════════════════════════════════
    // 1) البطاقة الشخصية النشطة
    // ═══════════════════════════════════════════════════════════
    $identityCard = \DB::table('identity_cards')
        ->where('citizen_id', $citizen->id)
        ->where('status', 'active')
        ->first();

    // ═══════════════════════════════════════════════════════════
    // 2) البطاقة العائلية (إذا كان رب أسرة)
    // ═══════════════════════════════════════════════════════════
    $familyCard = \DB::table('family_cards')
        ->where('head_id', $citizen->id)
        ->where('status', 'active')
        ->first();

    // ═══════════════════════════════════════════════════════════
    // 3) جواز السفر النشط
    // ═══════════════════════════════════════════════════════════
    $passport = \DB::table('passports')
        ->where('citizen_id', $citizen->id)
        ->where('status', 'active')
        ->first();

    // ═══════════════════════════════════════════════════════════
    // 4) شهادات الميلاد
    // ═══════════════════════════════════════════════════════════
    $birthCertificate = \DB::table('birth_certificates')
        ->where('child_id', $citizen->id)
        ->where('status', 'active')
        ->first();

    // ═══════════════════════════════════════════════════════════
    // 5) أعضاء العائلة
    // ═══════════════════════════════════════════════════════════
    $familyMembers = [];
    if ($familyCard) {
        $familyMembers = \DB::table('family_members')
            ->join('citizens', 'family_members.citizen_id', '=', 'citizens.id')
            ->where('family_members.family_card_id', $familyCard->id)
            ->where('family_members.is_active', true)
            ->select(
                'citizens.id',
                'citizens.first_name',
                'citizens.father_name',
                'citizens.middle_name',
                'citizens.last_name',
                'citizens.gender',
                'citizens.birth_date',
                'family_members.relationship'
            )
            ->get()
            ->map(fn($m) => [
                'full_name' => trim("{$m->first_name} {$m->father_name} {$m->middle_name} {$m->last_name}"),
                'gender' => $m->gender,
                'birth_date' => $m->birth_date,
                'relationship' => $m->relationship,
            ])
            ->toArray();
    }

    // ═══════════════════════════════════════════════════════════
    // 6) الإرجاع الكامل
    // ═══════════════════════════════════════════════════════════
    return [
        // ═══ الهوية الأساسية ═══
        'id' => $citizen->id,
        'full_name' => trim(sprintf(
            '%s %s %s %s',
            $citizen->first_name,
            $citizen->father_name,
            $citizen->middle_name,
            $citizen->last_name,
        )),
        'first_name' => $citizen->first_name,
        'father_name' => $citizen->father_name,
        'middle_name' => $citizen->middle_name,
        'last_name' => $citizen->last_name,
        'mother_name' => $citizen->mother_name,
        'full_name_en' => trim(sprintf(
            '%s %s %s %s',
            $citizen->first_name_en ?? '',
            $citizen->father_name_en ?? '',
            $citizen->middle_name_en ?? '',
            $citizen->last_name_en ?? '',
        )) ?: null,

        // ═══ البطاقة الشخصية ═══
        'national_id' => $identityCard->national_id ?? null,
        'identity_card' => $identityCard ? [
            'national_id' => $identityCard->national_id,
            'issue_date' => $identityCard->issue_date,
            'expiry_date' => $identityCard->expiry_date,
            'status' => $identityCard->status,
            'is_expired' => $identityCard->expiry_date
                ? now()->greaterThan($identityCard->expiry_date)
                : false,
        ] : null,

        // ═══ الميلاد ═══
        'birth_date' => $citizen->birth_date?->toDateString(),
        'birth_place' => $citizen->birth_place,
        'birth_place_en' => $citizen->birth_place_en,
        'age' => $citizen->birth_date
            ? $citizen->birth_date->age
            : null,

        // ═══ البيانات الشخصية ═══
        'gender' => $citizen->gender,
        'gender_label' => match ($citizen->gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => null,
        },
        'blood_type' => $citizen->blood_type,
        'marital_status' => $citizen->marital_status,
        'marital_status_label' => match ($citizen->marital_status) {
            'single' => 'أعزب',
            'married' => 'متزوج',
            'divorced' => 'مطلق',
            'widowed' => 'أرمل',
            default => null,
        },
        'occupation' => $citizen->occupation,
        'occupation_en' => $citizen->occupation_en,

        // ═══ التواصل ═══
        'address' => $citizen->address,
        'phone' => $citizen->phone,
        'email' => $citizen->email,

        // ═══ الصورة والحالة ═══
        'photo_url' => $citizen->photo
            ? url("/citizens/photo/" . basename($citizen->photo))
            : null,
        'verification_status' => $citizen->verification_status,
        'is_active' => (bool) $citizen->is_active,

        // ═══ البطاقة العائلية ═══
        'family_card' => $familyCard ? [
            'card_number' => $familyCard->card_number,
            'issue_date' => $familyCard->issue_date,
            'expiry_date' => $familyCard->expiry_date,
            'status' => $familyCard->status,
            'members_count' => count($familyMembers),
        ] : null,
        'family_members' => $familyMembers,

        // ═══ جواز السفر ═══
        'passport' => $passport ? [
            'passport_number' => $passport->passport_number,
            'issue_date' => $passport->issue_date,
            'expiry_date' => $passport->expiry_date,
            'type' => $passport->type,
            'status' => $passport->status,
            'issuing_authority' => $passport->issuing_authority_ar,
        ] : null,

        // ═══ شهادة الميلاد ═══
        'birth_certificate' => $birthCertificate ? [
            'certificate_number' => $birthCertificate->certificate_number,
            'issue_date' => $birthCertificate->issue_date,
            'status' => $birthCertificate->status,
        ] : null,

        // ═══ بيانات المطابقة ═══
        'confidence' => round($confidence, 4),
    ];
}
}