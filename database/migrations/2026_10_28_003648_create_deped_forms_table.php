<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deped_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Halimbawa: DepEd Form SY 2026-2027
            $table->string('version_tag'); // Halimbawa: v2026
            $table->string('file_path'); // Ang mismong PDF file para ma-download
            $table->json('form_fields'); // Dito nakalagay ang mga tanong/fields para sa bersyong ito
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deped_forms');
    }
};
