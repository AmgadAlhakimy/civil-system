<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('death_certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('deceased_id');
            $table->date('death_date');
            $table->string('cause_of_death', 200);
            $table->string('place_of_death', 100);
            $table->string('certificate_number', 20)->unique();
            $table->date('issue_date')->nullable();
            $table->string('status')->default('pending');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('qr_code')->nullable();
            $table->integer('print_count')->default(0);
            $table->uuid('issued_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('deceased_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('cascade');

            $table->foreign('issued_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index('deceased_id');
            $table->index('death_date');
            $table->index('issue_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('death_certificates');
    }
};
