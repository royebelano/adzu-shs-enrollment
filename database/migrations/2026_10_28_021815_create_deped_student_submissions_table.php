<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deped_student_submissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('deped_form_id');

            $table->json('answers');
            $table->string('generated_pdf_path')->nullable();

            $table->timestamps();

            $table->foreign('student_id')
                ->references('student_id')
                ->on('students')
                ->cascadeOnDelete();

            $table->foreign('deped_form_id')
                ->references('id')
                ->on('deped_forms')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deped_student_submissions');
    }
};
