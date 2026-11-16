<?php

namespace App\Services\Requests;

use App\Models\IdentityCard;
use App\Models\ServiceRequest;
use App\Models\User;

class IdentityCardRenewalHandler extends BaseServiceHandler
{
    public function getServiceTable(): string
    {
        return 'identity_cards';
    }












public function approve(ServiceRequest $request, User $reviewer): array
{
    \DB::beginTransaction();

    try {
        $data = $request->data ?? [];
        $cardNumber = $data['cardNumber'] ?? null;

        if (!$cardNumber) {
            \DB::rollBack();
            return ['success' => false, 'error' => 'رقم البطاقة مفقود'];
        }

        // 1. ابحث عن البطاقة الحالية
        $oldCard = IdentityCard::where('national_id', $cardNumber)->first();

        if (!$oldCard) {
            \DB::rollBack();
            return ['success' => false, 'error' => 'البطاقة غير موجودة'];
        }

        $citizen = $oldCard->citizen;

        if (!$citizen) {
            \DB::rollBack();
            return ['success' => false, 'error' => 'المواطن غير موجود'];
        }

        // 2. تحديث بيانات المواطن (العنوان قد تغير)
        $this->updateCitizen($citizen, $data);

        // 3. ✅ تحديث البطاقة الحالية بدلاً من إنشاء جديدة
        $oldCard->update([
            'branch_id' => $request->branch_id ?? $oldCard->branch_id,
            'issue_date' => now(),
            'expiry_date' => now()->addYears(10),
            'status' => 'active',
            'issued_by' => $reviewer->id,
            'approved_by' => $reviewer->id,
            'approved_at' => now(),
        ]);

        $request->update(['citizen_id' => $citizen->id]);

        \DB::commit();

        return [
            'success' => true,
            'citizen_id' => $citizen->id,
            'service_record_id' => $oldCard->id,
        ];
    } catch (\Throwable $e) {
        \DB::rollBack();
        \Log::error('IdentityCardRenewalHandler failed', [
            'error' => $e->getMessage(),
        ]);

        return ['success' => false, 'error' => $e->getMessage()];
    }
}


}
