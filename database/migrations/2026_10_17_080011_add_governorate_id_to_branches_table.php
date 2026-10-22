<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // إضافة governorate_id بعد code
            $table->uuid('governorate_id')
                ->nullable()
                ->after('code');

            // Foreign Key
            $table->foreign('governorate_id')
                ->references('id')
                ->on('governorates')
                ->nullOnDelete();

            // Index
            $table->index('governorate_id');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // حذف FK أولاً
            $table->dropForeign(['governorate_id']);
            $table->dropIndex(['governorate_id']);
            $table->dropColumn('governorate_id');
        });
    }
};