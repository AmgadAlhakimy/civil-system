<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // العلاقة مع المحافظة
            $table->uuid('governorate_id');

            // رمز المديرية (اختياري)
            $table->string('code', 20)->nullable();

            // الأسماء
            $table->string('name_ar', 150);
            $table->string('name_en', 150);

            // الحالة والترتيب
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key
            $table->foreign('governorate_id')
                ->references('id')
                ->on('governorates')
                ->cascadeOnDelete();

            // Indexes
            $table->index('governorate_id');
            $table->index('is_active');
            $table->index(['governorate_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};