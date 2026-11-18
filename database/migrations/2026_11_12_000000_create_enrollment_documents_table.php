<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();

            $table->string('type', 40);            // halimbawa: report_card, id_picture
            $table->string('original_name')->nullable();
            $table->string('path');                // nasa private storage
            $table->string('mime', 50);
            $table->unsignedInteger('size');

            $table->timestamps();

            $table->unique(['enrollment_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_documents');
    }
};
