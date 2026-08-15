<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // البطاقة العائلية
            $table->uuid('family_card_id');

            // المواطن
            $table->uuid('citizen_id');

            // صلة القرابة
            $table->enum('relationship', [
                'spouse',
                'child',
                'father',
                'mother',
                'brother',
                'sister',
                'other',
            ]);

            // حالة العضوية
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            // الموظف الذي أضاف الفرد
            $table->uuid('added_by');

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('family_card_id')
                ->references('id')
                ->on('family_cards')
                ->onDelete('cascade');

            $table->foreign('citizen_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('cascade');

            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            // Indexes
            $table->index('family_card_id');
            $table->index('citizen_id');
            $table->index('relationship');
            $table->index('is_active');

            // منع تكرار نفس المواطن في نفس البطاقة
            $table->unique([
                'family_card_id',
                'citizen_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
