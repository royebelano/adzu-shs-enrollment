<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_schedules', function (Blueprint $table) {
    $table->id();
    $table->date('schedule_date');
    $table->time('start_time'); // e.g. 13:00:00
    $table->time('end_time');   // e.g. 15:00:00
    $table->integer('max_slots')->default(15);
    $table->integer('walk_in_slots')->default(5);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_schedules');
    }
};
