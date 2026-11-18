<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'phone_number')) {
                $table->string('phone_number', 20)->nullable();
            }

            if (!Schema::hasColumn('enrollments', 'email')) {
                $table->string('email', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'phone_number')) {
                $table->dropColumn('phone_number');
            }

            if (Schema::hasColumn('enrollments', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
