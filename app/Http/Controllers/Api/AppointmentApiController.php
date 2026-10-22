<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentApiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // جلب مواعيد المستخدم
    // ═══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $appointments = Appointment::where('user_id', $request->user()->id)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $appointments,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // إنشاء موعد جديد
    // ═══════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required|string',
            'office' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string',
        ], [
            'service.required' => 'الخدمة مطلوبة',
            'office.required' => 'المكتب مطلوب',
            'appointment_date.required' => 'تاريخ الموعد مطلوب',
            'appointment_date.after_or_equal' => 'لا يمكن حجز موعد في تاريخ سابق',
            'appointment_time.required' => 'وقت الموعد مطلوب',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        // ✅ التحقق من عدم وجود تعارض
        $conflict = Appointment::where('office', $request->office)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الوقت محجوز بالفعل',
            ], 409);
        }

        $appointment = Appointment::create([
            'user_id' => $request->user()->id,
            'service' => $request->service,
            'office' => $request->office,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'reserved',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حجز الموعد بنجاح',
            'data' => $appointment,
        ], 201);
    }

    // ═══════════════════════════════════════════════════════════
    // جلب موعد واحد
    // ═══════════════════════════════════════════════════════════
    public function show(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'الموعد غير موجود',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $appointment,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // تحديث موعد
    // ═══════════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'الموعد غير موجود',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'sometimes|date|after_or_equal:today',
            'appointment_time' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $appointment->update($request->only([
            'appointment_date',
            'appointment_time',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الموعد بنجاح',
            'data' => $appointment->fresh(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // إلغاء موعد
    // ═══════════════════════════════════════════════════════════
    public function destroy(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'الموعد غير موجود',
            ], 404);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إلغاء الموعد بنجاح',
        ]);
    }
}