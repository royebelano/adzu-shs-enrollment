<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // ID ng estudyante na nag-book
            $table->unsignedBigInteger('schedule_id'); // ID mula sa appointment_schedules
            $table->enum('type', ['Online', 'Walk-In'])->default('Online'); // Uri ng pag-apply
            $table->string('status')->default('Confirmed'); // Confirmed, Completed, Cancelled
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('schedule_id')->references('id')->on('appointment_schedules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
