<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('code', 10)->unique();

            $table->string('name_ar', 100);
            $table->string('name_en', 100);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('name_ar');
            $table->index('name_en');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('governorates');
    }
};
