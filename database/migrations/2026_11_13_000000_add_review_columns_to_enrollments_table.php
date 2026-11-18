<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('tracking_number', 50)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique(['tracking_number']);
            $table->dropColumn([
                'phone_number',
                'email',
                'tracking_number',
            ]);
        });
    }
};
