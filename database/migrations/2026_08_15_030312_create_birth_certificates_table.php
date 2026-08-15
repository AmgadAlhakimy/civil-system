<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birth_certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('child_id');
            $table->uuid('father_id');
            $table->uuid('mother_id');
            $table->string('certificate_number', 20)->unique();
            $table->date('issue_date');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->text('qr_code')->nullable();
            $table->unsignedInteger('print_count')->default(0);
            $table->uuid('issued_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('child_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('cascade');

            $table->foreign('father_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('restrict');

            $table->foreign('mother_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('restrict');

            $table->foreign('issued_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->index('child_id');
            $table->index('father_id');
            $table->index('mother_id');
            $table->index('issue_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birth_certificates');
    }
};
