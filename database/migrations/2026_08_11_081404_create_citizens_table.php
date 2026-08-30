<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizens', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('national_id', 20)->unique();

            $table->string('first_name', 50);
            $table->string('middle_name', 50);
            $table->string('last_name', 50);
            $table->string('father_name', 100);
            $table->string('mother_name', 100);

            $table->date('birth_date');
            $table->string('birth_place', 100);

            $table->enum('gender', ['male', 'female']);

            $table->enum('marital_status', [
                'single',
                'married',
                'divorced',
                'widowed',
            ]);

            $table->string('occupation', 100)->nullable();

            $table->text('address');

            $table->string('phone', 20);
            $table->string('email', 100)->nullable();

            $table->string('photo')->nullable();
            $table->longText('face_data')->nullable();
            $table->longText('fingerprint_data')->nullable();

            $table->boolean('is_active')->default(false);

            $table->timestamp('verified_at')->nullable();

            $table->uuid('verified_by')->nullable();

            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('birth_date');
            $table->index('gender');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
