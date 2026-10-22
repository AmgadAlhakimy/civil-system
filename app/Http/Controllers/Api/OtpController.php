<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Services\SmsGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{





    public function send(Request $request, SmsGatewayService $sms)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $phone = $this->normalizePhone($data['phone']);

        if (!$phone) {
            throw ValidationException::withMessages([
                'phone' => ['أدخل رقم جوال يمنيًا صحيحًا.'],
            ]);
        }

        $key = 'otp-send:' . hash('sha256', $phone);

        if (RateLimiter::tooManyAttempts($key, 1)) {
            return response()->json([
                'message' => 'انتظر دقيقة قبل طلب رمز جديد.',
            ], 429);
        }

        $code = (string) random_int(100000, 999999);

        $otp = OtpCode::create([
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
            'last_sent_at' => now(),
            'attempts' => 0,
        ]);

        try {
            $sms->send(
                $phone,
                "رمز التحقق: {$code}. صالح لمدة 5 دقائق."
            );
        } catch (\Throwable $e) {
            report($e);
            $otp->delete();

            return response()->json([
                'message' => 'تعذر إرسال الرسالة. تحقق من إعدادات SMS Gateway.',
            ], 502);
        }

        RateLimiter::hit($key, 60);

        return response()->json([
            'message' => 'تم إرسال طلب رمز التحقق.',
            'expires_in' => 300,
        ], 202);
    }






public function verify(Request $request)
{
    $data = $request->validate([
        'phone' => ['required', 'string', 'max:30'],
        'code' => ['required', 'digits:6'],
    ]);

    $phone = $this->normalizePhone($data['phone']);

    if (!$phone) {
        return response()->json([
            'message' => 'رقم الهاتف غير صحيح.',
        ], 422);
    }

    $key = 'otp-verify:' . hash('sha256', $phone);

    if (RateLimiter::tooManyAttempts($key, 5)) {
        return response()->json([
            'message' => 'تجاوزت عدد المحاولات. حاول لاحقًا.',
        ], 429);
    }

    $otp = OtpCode::where('phone', $phone)
        ->whereNull('verified_at')
        ->latest('id')
        ->first();

    if (!$otp || $otp->expires_at->isPast()) {
        return response()->json([
            'message' => 'الرمز غير صالح أو انتهت صلاحيته.',
        ], 422);
    }

    if ($otp->attempts >= 5) {
        return response()->json([
            'message' => 'انتهت محاولات هذا الرمز. اطلب رمزًا جديدًا.',
        ], 429);
    }

    $otp->increment('attempts');

    if (!Hash::check($data['code'], $otp->code_hash)) {
        RateLimiter::hit($key, 300);

        return response()->json([
            'message' => 'رمز التحقق غير صحيح.',
        ], 422);
    }

    $otp->update([
        'verified_at' => now(),
    ]);

    RateLimiter::clear($key);

    return response()->json([
        'message' => 'تم التحقق من رقم الهاتف بنجاح.',
        'verified' => true,
    ]);
}

    private function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/[\s\-()]/', '', $phone);

        if (str_starts_with($phone, '00967')) {
            $phone = '+' . substr($phone, 2);
        } elseif (str_starts_with($phone, '967')) {
            $phone = '+' . $phone;
        } elseif (preg_match('/^7\d{8}$/', $phone)) {
            $phone = '+967' . $phone;
        }

        return preg_match('/^\+9677\d{8}$/', $phone)
            ? $phone
            : null;
    }
}
