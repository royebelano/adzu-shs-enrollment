<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per hour of one date. Admission staff set each date by hand.
        if (!Schema::hasTable('capacity_slots')) {
            Schema::create('capacity_slots', function (Blueprint $table) {
                $table->id();
                $table->date('date')->index();
                $table->string('start_time', 5);                 // "08:00"
                $table->string('mode', 10)->default('online');   // online | walkin | closed
                $table->unsignedInteger('capacity')->default(10);
                $table->timestamps();

                $table->unique(['date', 'start_time']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('capacity_slots');
    }
};
