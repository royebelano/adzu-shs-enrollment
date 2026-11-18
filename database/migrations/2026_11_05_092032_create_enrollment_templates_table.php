<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Burahin muna kung meron man para hindi mag-error
        Schema::dropIfExists('enrollment_templates');

        Schema::create('enrollment_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_templates');
    }
};
