<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Default admission schedule (one row).
        if (!Schema::hasTable('capacity_settings')) {
            Schema::create('capacity_settings', function (Blueprint $table) {
                $table->id();
                $table->string('open_time', 5)->default('08:00');
                $table->string('close_time', 5)->default('16:00');
                $table->unsignedInteger('capacity_per_hour')->default(30);
                $table->unsignedInteger('walk_in_reserve')->default(5);
                $table->string('open_days', 20)->default('1,2,3,4,5'); // ISO: 1 = Monday ... 7 = Sunday
                $table->unsignedSmallInteger('booking_days')->default(30);
                $table->timestamps();
            });
        }

        // Changes for one date: close the day, close or resize one hour.
        if (!Schema::hasTable('capacity_overrides')) {
            Schema::create('capacity_overrides', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->string('start_time', 5)->nullable(); // null = the whole day
                $table->boolean('is_closed')->default(false);
                $table->unsignedInteger('capacity')->nullable();
                $table->unsignedInteger('walk_in_reserve')->nullable();
                $table->string('note')->nullable();
                $table->timestamps();

                $table->unique(['date', 'start_time']);
            });
        }

        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'appointment_date')) {
                $table->date('appointment_date')->nullable()->index();
            }

            if (!Schema::hasColumn('enrollments', 'appointment_time')) {
                $table->string('appointment_time', 5)->nullable();
            }

            if (!Schema::hasColumn('enrollments', 'is_walk_in')) {
                $table->boolean('is_walk_in')->default(false);
            }

            if (!Schema::hasColumn('enrollments', 'walk_in_by')) {
                $table->string('walk_in_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacity_overrides');
        Schema::dropIfExists('capacity_settings');

        Schema::table('enrollments', function (Blueprint $table) {
            foreach (['appointment_date', 'appointment_time', 'is_walk_in', 'walk_in_by'] as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
