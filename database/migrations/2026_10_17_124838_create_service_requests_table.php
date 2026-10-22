<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();

            // ═══════════════════════════════════════════════════════
            // العلاقات
            // ═══════════════════════════════════════════════════════
            $table->uuid('user_id');                              // صاحب الطلب
            $table->uuid('citizen_id')->nullable();               // يُنشأ عند الموافقة
            $table->uuid('branch_id')->nullable();                // الفرع المختار
            $table->uuid('reviewed_by')->nullable();              // المراجع

            // ═══════════════════════════════════════════════════════
            // بيانات الطلب
            // ═══════════════════════════════════════════════════════
            $table->string('request_number', 50)->unique();       // ID-YYYYMMDD-XXXX
            $table->string('service_key', 100);                   // new_id_card, renew_passport...
            $table->string('service_name', 255);                  // اسم الخدمة بالعربي
            $table->string('category', 50);                       // id_card, passport, family_card, certificate
            $table->string('title', 255);                         // عنوان الطلب

            // ═══════════════════════════════════════════════════════
            // الدفع
            // ═══════════════════════════════════════════════════════
            $table->decimal('amount', 10, 2)->default(0);         // السعر
            $table->string('currency', 3)->default('YER');        // العملة
            $table->string('payment_method', 50)->nullable();     // wallet, bank, cash
            $table->string('payment_status', 20)->default('pending'); // pending, paid, failed

            // ═══════════════════════════════════════════════════════
            // حالة الطلب
            // ═══════════════════════════════════════════════════════
            $table->string('status', 30)->default('pending');     // pending, processing, approved, rejected
            $table->text('rejection_reason')->nullable();         // سبب الرفض
            $table->timestamp('reviewed_at')->nullable();         // وقت المراجعة

            // ═══════════════════════════════════════════════════════
            // البيانات (JSON)
            // ═══════════════════════════════════════════════════════
            $table->json('data')->nullable();                     // كل بيانات الطلب
            $table->json('documents')->nullable();                // مسارات المستندات

            // ═══════════════════════════════════════════════════════
            // Timestamps
            // ═══════════════════════════════════════════════════════
            $table->timestamps();
            $table->softDeletes();

            // ═══════════════════════════════════════════════════════
            // Foreign Keys
            // ═══════════════════════════════════════════════════════
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('citizen_id')
                ->references('id')
                ->on('citizens')
                ->nullOnDelete();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->nullOnDelete();

            $table->foreign('reviewed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // ═══════════════════════════════════════════════════════
            // Indexes
            // ═══════════════════════════════════════════════════════
            $table->index('user_id');
            $table->index('request_number');
            $table->index('service_key');
            $table->index('category');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['user_id', 'status']);
            $table->index(['service_key', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};