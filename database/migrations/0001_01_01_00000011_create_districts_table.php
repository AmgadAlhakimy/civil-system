<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('governorate_id');

            $table->string('code', 20)->nullable()->unique();

            $table->string('name_ar', 150);
            $table->string('name_en', 150);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('governorate_id')
                ->references('id')
                ->on('governorates')
                ->cascadeOnDelete();

            $table->index('name_ar');
            $table->index('name_en');
            $table->index('is_active');
            $table->index(['governorate_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
