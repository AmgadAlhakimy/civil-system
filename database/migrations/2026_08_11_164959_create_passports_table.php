<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('passports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // المواطن
            $table->uuid('citizen_id');

            // بيانات الجواز
            $table->string('passport_number', 20)->unique();
            $table->enum('type', [
                'ordinary',
                'diplomatic',
                'official',
            ]);

            // حالة الجواز
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'active',
                'expired',
                'cancelled',
                'lost',
                'damaged',
            ])->default('pending');

            // يتم تحديدهما عند الموافقة على الجواز
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            // معلومات إضافية
            $table->text('notes')->nullable();
            $table->text('qr_code')->nullable();

            // الطباعة
            $table->integer('print_count')->default(0);

            // الموظف الذي أنشأ طلب الجواز
            $table->uuid('issued_by');

            // الموظف الذي وافق على الجواز
            $table->uuid('approved_by')->nullable();

            // تاريخ الموافقة
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // العلاقات
            $table->foreign('citizen_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('cascade');

            $table->foreign('issued_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // الفهارس
            $table->index('status');
            $table->index('issue_date');
            $table->index('expiry_date');
            $table->index('citizen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passports');
    }
};
