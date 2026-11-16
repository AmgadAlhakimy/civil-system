<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_log', 'causer_name')) {
                $table->string('causer_name')->nullable()->after('causer_type');
            }
            if (! Schema::hasColumn('activity_log', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('causer_name');
            }
            if (! Schema::hasColumn('activity_log', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (! Schema::hasColumn('activity_log', 'device_type')) {
                $table->string('device_type', 50)->nullable()->after('user_agent');
            }
            if (! Schema::hasColumn('activity_log', 'browser')) {
                $table->string('browser', 100)->nullable()->after('device_type');
            }
            if (! Schema::hasColumn('activity_log', 'user_role')) {
                $table->string('user_role', 100)->nullable()->after('browser');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $columns = ['causer_name', 'ip_address', 'user_agent', 'device_type', 'browser', 'user_role'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('activity_log', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};