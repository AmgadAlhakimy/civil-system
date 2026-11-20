<?php

namespace App\Services\Requests;

use App\Models\IdentityCard;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IdentityCardIssueHandler extends BaseServiceHandler
{
    public function getServiceTable(): string
    {
        return 'identity_cards';
    }

    public function approve(ServiceRequest $request, User $reviewer): array
    {
        DB::beginTransaction();

        try {
            // 1. إنشاء/تحديث المواطن مع إبقائه قيد الانتظار
            $citizen = $this->findOrCreateCitizen($request);

            if (!$citizen) {
                DB::rollBack();

                return [
                    'success' => false,
                    'error' => 'فشل إنشاء بيانات المواطن',
                ];
            }

            // 2. إنشاء سجل البطاقة بحالة قيد الانتظار
            // الاعتماد النهائي يتم بعد إدخال فصيلة الدم والتحقق من الصورة.
            $card = IdentityCard::create([
                'citizen_id' => $citizen->id,
                'national_id' => $this->generateNationalId(),
                'branch_id' => $request->branch_id,
                'issue_date' => now(),
                'expiry_date' => now()->addYears(10),
                'status' => 'pending',
                'issued_by' => $reviewer->id,
                'approved_by' => null,
                'approved_at' => null,
            ]);

            // 3. ربط الطلب بالمواطن
            $request->update([
                'citizen_id' => $citizen->id,
            ]);

            DB::commit();

            return [
                'success' => true,
                'citizen_id' => $citizen->id,
                'service_record_id' => $card->id,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('IdentityCardIssueHandler failed', [
                'error' => $e->getMessage(),
                'request_id' => $request->id,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function generateNationalId(): string
    {
        do {
            $id = '0';

            for ($i = 0; $i < 10; $i++) {
                $id .= random_int(0, 9);
            }
        } while (IdentityCard::where('national_id', $id)->exists());

        return $id;
    }
}
