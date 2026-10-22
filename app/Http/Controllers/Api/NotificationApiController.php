<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // جلب إشعارات المستخدم
    // ═══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->data['title'] ?? '',
                'body' => $n->data['body'] ?? '',
                'stage' => $n->data['stage'] ?? 'general',
                'icon' => $n->data['icon'] ?? 'notification_important',
                'color' => $n->data['color'] ?? 'info',
                'request_id' => $n->data['request_id'] ?? null,
                'request_number' => $n->data['request_number'] ?? null,
                'service_name' => $n->data['service_name'] ?? null,
                'old_status' => $n->data['old_status'] ?? null,
                'new_status' => $n->data['new_status'] ?? null,
                'old_payment_status' => $n->data['old_payment_status'] ?? null,
                'new_payment_status' => $n->data['new_payment_status'] ?? null,
                'rejection_reason' => $n->data['rejection_reason'] ?? null,
                'is_read' => $n->read_at !== null,
                'read_at' => $n->read_at?->toIso8601String(),
                'created_at' => $n->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'status' => 'success',
            'unread_count' => $user->unreadNotifications()->count(),
            'data' => $notifications,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // عدد الإشعارات غير المقروءة (للبادج)
    // ═══════════════════════════════════════════════════════════
    public function unreadCount(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // تعليم إشعار كمقروء
    // ═══════════════════════════════════════════════════════════
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'الإشعار غير موجود',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديد الإشعار كمقروء',
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // تعليم الكل كمقروء
    // ═══════════════════════════════════════════════════════════
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديد جميع الإشعارات كمقروءة',
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // حذف إشعار
    // ═══════════════════════════════════════════════════════════
    public function destroy(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'الإشعار غير موجود',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الإشعار',
        ]);
    }
}