<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('enrollments', 'tracking_number')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('tracking_number', 50)->nullable()->unique();
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: the column may belong to an earlier migration.
    }
};
