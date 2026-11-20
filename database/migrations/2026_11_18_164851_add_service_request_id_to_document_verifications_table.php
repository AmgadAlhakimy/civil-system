<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_verifications', function (Blueprint $table) {
            $table->uuid('service_request_id')->nullable()->after('user_id');
            $table->string('document_reference', 500)->nullable()->after('service_request_id');

            $table->foreign('service_request_id')
                ->references('id')
                ->on('service_requests')
                ->nullOnDelete();

            $table->index('service_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('document_verifications', function (Blueprint $table) {
            $table->dropForeign(['service_request_id']);
            $table->dropIndex(['service_request_id']);
            $table->dropColumn(['service_request_id', 'document_reference']);
        });
    }
};
