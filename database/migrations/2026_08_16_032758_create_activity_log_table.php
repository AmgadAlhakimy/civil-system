<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->create(config('activitylog.table_name'), function (Blueprint $table) {

                $table->bigIncrements('id');

                $table->string('log_name')->nullable();

                $table->text('description');

                $table->string('subject_type')->nullable();
                $table->uuid('subject_id')->nullable();

                $table->index([
                    'subject_type',
                    'subject_id',
                ]);

                $table->string('causer_type')->nullable();
                $table->uuid('causer_id')->nullable();

                $table->index([
                    'causer_type',
                    'causer_id',
                ]);

                $table->json('properties')->nullable();

                $table->string('event')->nullable();

                $table->uuid('batch_uuid')->nullable();

                $table->ipAddress('ip_address')->nullable();

                $table->text('user_agent')->nullable();

                $table->string('device_type', 20)->nullable();

                $table->string('browser', 50)->nullable();

                $table->string('user_role')->nullable();

                $table->timestamps();

                $table->index('log_name');
                $table->index('event');
                $table->index('created_at');
            });
    }

    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->dropIfExists(config('activitylog.table_name'));
    }
};
