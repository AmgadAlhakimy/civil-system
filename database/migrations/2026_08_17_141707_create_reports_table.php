<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('report_type', 50);
            $table->string('format', 20)->nullable();
            $table->string('path')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->json('filters')->nullable();

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
            ])->default('pending');

            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index('report_type');
            $table->index('status');
            $table->index('generated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
