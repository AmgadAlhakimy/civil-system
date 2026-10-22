<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_fees', function (Blueprint $table) {
            // Primary Key (UUID)
            $table->uuid('id')->primary();

            // معرّف الخدمة (للربط مع Flutter)
            // أمثلة: new_id_card, renew_passport, birth_certificate
            $table->string('service_key')->unique();

            // الاسم الظاهر في الواجهات
            $table->string('service_name');

            // الفئة: id_card, passport, family_card, certificate
            $table->string('category');

            // السعر
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('YER');

            // الحالة
            $table->boolean('is_active')->default(true);

            // ملاحظات (تظهر للمدير فقط)
            $table->text('notes')->nullable();

            // من آخر من عدّل (UUID المستخدم)
            $table->uuid('updated_by')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('service_key');
            $table->index('category');
            $table->index('is_active');

            // Foreign Key (اختياري - على users)
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fees');
    }
};