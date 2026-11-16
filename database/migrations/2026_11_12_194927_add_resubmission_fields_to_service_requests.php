<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->unsignedTinyInteger('resubmission_count')
                ->default(0)
                ->after('rejection_reason');

            $table->timestamp('last_resubmitted_at')
                ->nullable()
                ->after('resubmission_count');

            $table->boolean('is_resubmittable')
                ->default(false)
                ->after('last_resubmitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'resubmission_count',
                'last_resubmitted_at',
                'is_resubmittable',
            ]);
        });
    }
};