<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('head_id');

            $table->string('card_number', 20)->unique();

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

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('head_id')
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

            $table->index('status');
            $table->index('issue_date');
            $table->index('expiry_date');
            $table->index('head_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_cards');
    }
};
