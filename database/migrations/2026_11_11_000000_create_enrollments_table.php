<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            // mga column na madalas hanapin / i-filter
            $table->string('school_year', 9)->nullable();
            $table->string('grade_level', 2)->nullable();
            $table->string('lrn', 12)->nullable()->index();
            $table->string('last_name')->index();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('extension_name')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('sex', 6)->nullable();

            // ang buong laman ng form (lahat ng field at checkbox)
            $table->json('data');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
