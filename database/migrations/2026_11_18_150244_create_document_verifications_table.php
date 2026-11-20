<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('document_type', 100);

            $table->char('sha256', 64);

            $table->string('mime_type', 100)->nullable();

            $table->unsignedBigInteger('file_size')->nullable();

            $table->unsignedInteger('image_width')->nullable();

            $table->unsignedInteger('image_height')->nullable();

            $table->string('status', 50);

            $table->json('checks')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'sha256']);

            $table->index('document_type');

            $table->index('status');

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
    }
};