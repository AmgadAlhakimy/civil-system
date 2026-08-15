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

            // رب الأسرة
            $table->uuid('head_id');

            // بيانات البطاقة
            $table->string('card_number', 20)->unique();
            $table->date('issue_date');
            $table->date('expiry_date');

            // حالة البطاقة
            $table->enum('status', [
                'pending',
                'active',
                'expired',
                'cancelled',
            ])->default('pending');

            $table->text('notes')->nullable();
            $table->text('qr_code')->nullable();
            $table->integer('print_count')->default(0);

            // الإصدار والاعتماد
            $table->uuid('issued_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
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

            // Indexes
            $table->index('status');
            $table->index('head_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_cards');
    }
};
