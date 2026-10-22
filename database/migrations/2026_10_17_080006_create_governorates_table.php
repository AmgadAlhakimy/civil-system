<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // رمز المحافظة (مثل: SNA, ADN)
            $table->string('code', 10)->unique();

            // الأسماء
            $table->string('name_ar', 100);
            $table->string('name_en', 100);

            // الحالة والترتيب
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('governorates');
    }
};