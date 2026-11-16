<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_request_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->uuid('changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('service_request_id')
                ->references('id')
                ->on('service_requests')
                ->cascadeOnDelete();

            $table->foreign('changed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index('service_request_id');
            $table->index('to_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_status_history');
    }
};