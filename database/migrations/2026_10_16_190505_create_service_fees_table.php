<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_name', 150);

            $table->enum('category', [
                'identity_card',
                'passport',
                'family_card',
                'birth_certificate',
                'death_certificate',
                'other',
            ]);

            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->uuid('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('is_active');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fees');
    }
};
