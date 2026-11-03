<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('code', 20)->unique();
            $table->string('name', 100);

            $table->uuid('governorate_id')->nullable();

            $table->foreign('governorate_id')
                ->references('id')
                ->on('governorates')
                ->nullOnDelete();

            $table->text('address');
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('governorate_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
