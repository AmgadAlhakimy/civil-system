<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */



// database/migrations/xxxx_make_service_key_nullable_in_service_fees.php
public function up(): void
{
    Schema::table('service_fees', function (Blueprint $table) {
        $table->string('service_key')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('service_fees', function (Blueprint $table) {
        $table->string('service_key')->nullable(false)->change();
    });
}

};
