<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('citizen_id');
            $table->uuid('branch_id');
            $table->uuid('user_id')->nullable();

            $table->enum('service_type', [
                'passport_new',
                'passport_renew',
                'passport_lost',
                'passport_damaged',
                'national_id_new',
                'national_id_renew',
                'national_id_lost',
                'national_id_damaged',
                'family_card_new',
                'family_card_renew',
                'birth_certificate',
                'death_certificate',
            ]);

            $table->date('appointment_date');
            $table->time('appointment_time');

            $table->enum('status', [
                'pending',
                'confirmed',
                'attended',
                'cancelled',
                'no_show',
            ])->default('pending');

            $table->text('notes')->nullable();
            $table->text('qr_code')->nullable();

            $table->uuid('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('citizen_id')
                ->references('id')
                ->on('citizens')
                ->onDelete('cascade');

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('restrict');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('confirmed_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('citizen_id');
            $table->index('branch_id');
            $table->index('user_id');
            $table->index('appointment_date');
            $table->index('status');
            $table->index('service_type');

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Appointment Slot
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'branch_id',
                'appointment_date',
                'appointment_time',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
