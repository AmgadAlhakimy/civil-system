<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\SmsGatewayService;
class AuthController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // تسجيل حساب جديد
    // ═══════════════════════════════════════════════════════════


public function register(Request $request, SmsGatewayService $sms)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255|min:3',
        'phone' => ['required', 'string', 'regex:/^7[0-9]{8}$/', 'unique:users,phone'],
        'email' => 'nullable|email|unique:users,email',
        'password' => 'required|string|min:8',
    ], [
        'name.required' => 'الاسم مطلوب',
        'name.min' => 'الاسم يجب أن يكون 3 أحرف على الأقل',
        'phone.required' => 'رقم الهاتف مطلوب',
        'phone.unique' => 'رقم الهاتف مسجل بالفعل',
        'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ 7 ويتكون من 9 أرقام',
        'email.email' => 'البريد الإلكتروني غير صحيح',
        'email.unique' => 'البريد الإلكتروني مسجل بالفعل',
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'بيانات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422);
    }

    $otpCode = str_pad(
        (string) random_int(0, 999999),
        6,
        '0',
        STR_PAD_LEFT
    );

    $user = User::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'otp_code' => $otpCode,
        'otp_expires_at' => now()->addMinutes(10),
        'is_verified' => false,
        'status' => 'active',
    ]);

    try {
        $sms->send(
            '+967' . $user->phone,
            "رمز التحقق: {$otpCode}. صالح لمدة 10 دقائق."
        );
    } catch (\Throwable $e) {
        report($e);

        // لا نترك حسابًا غير موثق بلا إمكانية واضحة لإعادة المحاولة.
        $user->delete();

        return response()->json([
            'status' => 'error',
            'message' => 'تعذر إرسال رمز التحقق. حاول مرة أخرى لاحقًا.',
        ], 502);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'تم إنشاء الحساب وإرسال رمز التحقق إلى هاتفك.',
        'data' => [
            'user_id' => $user->id,
            'phone' => $user->phone,
        ],
    ], 201);
}


    // ═══════════════════════════════════════════════════════════
    // التحقق من OTP
    // ═══════════════════════════════════════════════════════════
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp_code' => 'required|string|size:6',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'otp_code.required' => 'كود التحقق مطلوب',
            'otp_code.size' => 'كود التحقق يجب أن يكون 6 أرقام',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('phone', $request->phone)
            ->where('otp_code', $request->otp_code)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'كود التحقق غير صحيح أو منتهي الصلاحية',
            ], 401);
        }

        $user->update([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // ✅ إنشاء توكن Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم التحقق بنجاح',
            'data' => [
                'user' => $user->fresh(),
                'token' => $token,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // إعادة إرسال OTP
    // ═══════════════════════════════════════════════════════════
    
public function resendOtp(
    Request $request,
    SmsGatewayService $sms
) {
    $validator = Validator::make($request->all(), [
        'phone' => 'required|string|regex:/^7[0-9]{8}$/|exists:users,phone',
    ], [
        'phone.required' => 'رقم الهاتف مطلوب',
        'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ 7 ويتكون من 9 أرقام',
        'phone.exists' => 'رقم الهاتف غير مسجل',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'بيانات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422);
    }

    $user = User::where('phone', $request->phone)
        ->where('is_verified', false)
        ->first();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'لا يوجد حساب غير موثق لهذا الرقم.',
        ], 422);
    }

    $otpCode = str_pad(
        (string) random_int(0, 999999),
        6,
        '0',
        STR_PAD_LEFT
    );

    $user->update([
        'otp_code' => $otpCode,
        'otp_expires_at' => now()->addMinutes(10),
    ]);

    try {
        $sms->send(
            '+967' . $user->phone,
            "رمز التحقق: {$otpCode}. صالح لمدة 10 دقائق."
        );
    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'status' => 'error',
            'message' => 'تعذر إرسال رمز التحقق. حاول لاحقًا.',
        ], 502);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'تم إرسال رمز تحقق جديد إلى هاتفك.',
        'data' => [
            'phone' => $user->phone,
        ],
    ]);
}




    /**
     * تسجيل الدخول
     */
    public function login(Request $request, SmsGatewayService $sms)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^7[0-9]{8}$/'],
            'password' => ['required', 'string'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.regex' => 'أدخل رقم هاتف صحيحًا مكونًا من 9 أرقام ويبدأ بـ 7.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات الدخول غير صحيحة.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم الهاتف أو كلمة المرور غير صحيحة.',
            ], 401);
        }

        if (!$user->is_verified) {
            $otpCode = str_pad(
                (string) random_int(0, 999999),
                6,
                '0',
                STR_PAD_LEFT
            );

            $user->update([
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            try {
                $sms->send(
                    '+967' . $user->phone,
                    "رمز التحقق: {$otpCode}. صالح لمدة 10 دقائق."
                );
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'status' => 'error',
                    'message' => 'تعذر إرسال رمز التحقق. حاول لاحقًا.',
                ], 502);
            }

            return response()->json([
                'status' => 'success',
                'requires_verification' => true,
                'message' => 'حسابك غير موثق. تم طلب إرسال رمز التحقق إلى هاتفك.',
                'data' => [
                    'user_id' => $user->id,
                    'phone' => $user->phone,
                ],
            ]);
        }

        if (isset($user->status) && $user->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'حسابك غير نشط. يرجى التواصل مع الإدارة.',
            ], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // بيانات المستخدم الحالي
    // ═══════════════════════════════════════════════════════════
    public function user(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // تسجيل الخروج
    // ═══════════════════════════════════════════════════════════
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // تحديث الملف الشخصي
    // ═══════════════════════════════════════════════════════════
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|min:3',
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ], [
            'name.min' => 'الاسم يجب أن يكون 3 أحرف على الأقل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مسجل بالفعل',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update($request->only(['name', 'full_name', 'email']));

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث البيانات بنجاح',
            'data' => $user->fresh(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // تغيير كلمة المرور
    // ═══════════════════════════════════════════════════════════
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ], [
            'old_password.required' => 'كلمة المرور الحالية مطلوبة',
            'new_password.required' => 'كلمة المرور الجديدة مطلوبة',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'كلمة المرور الحالية غير صحيحة',
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // ✅ حذف جميع التوكنات (إجبار إعادة تسجيل الدخول)
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تغيير كلمة المرور بنجاح. يرجى إعادة تسجيل الدخول.',
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // رفع مستند
    // ═══════════════════════════════════════════════════════════
    public function uploadDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'document_type' => 'required|string',
        ], [
            'document.required' => 'المستند مطلوب',
            'document.file' => 'الملف غير صالح',
            'document.mimes' => 'يُسمح فقط بـ jpg, jpeg, png, pdf',
            'document.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت',
            'document_type.required' => 'نوع المستند مطلوب',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('document');
        $path = $file->store('documents/' . $request->user()->id, 'public');

        return response()->json([
            'status' => 'success',
            'message' => 'تم رفع المستند بنجاح',
            'data' => [
                'path' => $path,
                'url' => asset('storage/' . $path),
                'document_type' => $request->document_type,
            ],
        ]);
    }
}