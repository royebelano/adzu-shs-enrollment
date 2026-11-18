<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('students', function (Blueprint $table) {
        if (!Schema::hasColumn('students', 'editable_student_id')) {
            $table->unsignedBigInteger('editable_student_id')->nullable();
        }
        if (!Schema::hasColumn('students', 'school')) {
            $table->string('school')->nullable();
        }
        if (!Schema::hasColumn('students', 'suffix')) {
            $table->string('suffix')->nullable();
        }
        if (!Schema::hasColumn('students', 'gco_assessment_date')) {
            $table->date('gco_assessment_date')->nullable();
        }
        if (!Schema::hasColumn('students', 'physical_exam')) {
            $table->date('physical_exam')->nullable();
        }
        if (!Schema::hasColumn('students', 'dental_exam')) {
            $table->date('dental_exam')->nullable();
        }
        if (!Schema::hasColumn('students', 'medical_certificate')) {
            $table->date('medical_certificate')->nullable();
        }
        if (!Schema::hasColumn('students', 'form_138')) {
            $table->boolean('form_138')->default(false);
        }
        if (!Schema::hasColumn('students', 'psa')) {
            $table->boolean('psa')->default(false);
        }
        if (!Schema::hasColumn('students', 'good_moral')) {
            $table->boolean('good_moral')->default(false);
        }
        if (!Schema::hasColumn('students', 'admission_step3_status')) {
            $table->string('admission_step3_status')->nullable();
        }
        if (!Schema::hasColumn('students', 'contact_person')) {
            $table->string('contact_person')->nullable();
        }
        if (!Schema::hasColumn('students', 'contact_address')) {
            $table->string('contact_address')->nullable();
        }
        if (!Schema::hasColumn('students', 'contact_relationship')) {
            $table->string('contact_relationship')->nullable();
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
