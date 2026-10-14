<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identity_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('citizen_id');

            $table->string('national_id', 11)->unique();

            $table->enum('status', [
                'pending',
                'rejected',
                'active',
                'expired',
                'cancelled',
                'lost',
                'damaged',
            ])->default('pending');

            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->text('notes')->nullable();
            $table->text('qr_code')->nullable();

            $table->integer('print_count')->default(0);

            $table->uuid('issued_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->uuid('branch_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('citizen_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('cascade');

            $table->foreign('issued_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('set null');

            $table->index('status');
            $table->index('issue_date');
            $table->index('expiry_date');
            $table->index('citizen_id');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('identity_cards');
    }
};
