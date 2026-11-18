<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('students', function (Blueprint $table) {
        $table->id('student_id');
        $table->unsignedBigInteger('editable_student_id')->nullable();
        $table->string('reference_id')->nullable();
        $table->string('lrn')->nullable();
        $table->string('last_name');
        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('suffix')->nullable();
        $table->date('date_of_birth')->nullable();
        $table->string('gender')->nullable();
        $table->string('contact_number')->nullable();
        $table->string('email')->nullable();
        $table->string('preferred_strand')->nullable();
        $table->string('school')->nullable();

        // Medical assessment date (para sa date picker)
        $table->date('medical_assessment_date')->nullable();

        // Mga checkboxes sa Infirmary (ginawang boolean para maiwasan ang invalid date format error)
        $table->boolean('physical_exam')->default(false);
        $table->boolean('dental_exam')->default(false);
        $table->boolean('medical_certificate')->default(false);

        $table->string('status')->default('Pending');
        $table->timestamps();
    });
}
};
