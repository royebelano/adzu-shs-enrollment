<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id('document_id'); // Primary Key
            $table->unsignedBigInteger('student_id');
            $table->string('document_name');
            $table->string('file_path');
            $table->enum('status', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
